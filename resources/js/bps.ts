import { crc32 } from "@stardazed/crc32";

export default class BPS {
    static readonly ACTION_SOURCE_READ = 0;
    static readonly ACTION_TARGET_READ = 1;
    static readonly ACTION_SOURCE_COPY = 2;
    static readonly ACTION_TARGET_COPY = 3;

    private sourceSize: number;
    private targetSize: number;
    private metaDataString: string;
    public  meta: object;
    private actionsOffset: number;
    private file: Uint8Array;
    private sourceChecksum: number;
    private targetChecksum: number;
    private patchChecksum: number;

    constructor(file: ArrayBuffer) {
        this.sourceSize = 0;
        this.targetSize = 0;
        this.metaDataString = '';
        this.meta = {};
        this.actionsOffset = 0;
        this.file = new Uint8Array(file);
        this.sourceChecksum = 0;
        this.targetChecksum = 0;
        this.patchChecksum = 0;


        let seek = 4; // skip BPS1
        let decodedSourceSize = this.decodeBPS(this.file, seek);
        this.sourceSize = decodedSourceSize.number;
        seek += decodedSourceSize.length;
        let decodedTargetSize = this.decodeBPS(this.file, seek);
        this.targetSize = decodedTargetSize.number;
        seek += decodedTargetSize.length;

        let decodedMetaDataLength = this.decodeBPS(this.file, seek);

        seek += decodedMetaDataLength.length;
        if (decodedMetaDataLength.number) {
            let metaArray = this.file.slice(seek, seek + decodedMetaDataLength.number);
            for (let i = 0; i < metaArray.byteLength; ++i) {
                this.metaDataString += String.fromCharCode(metaArray[i]);
            }
            this.meta = JSON.parse(this.metaDataString);
            seek += decodedMetaDataLength.number;
        }

        this.actionsOffset = seek;

        let buf32 = new Int32Array(file.slice(file.byteLength - 12));

        this.sourceChecksum = buf32[0];
        this.targetChecksum = buf32[1];
        this.patchChecksum = buf32[2];

        if (this.patchChecksum !== crc32(this.file.slice(0, this.file.byteLength - 4))) {
            throw new Error('Patch checksum incorrect');
        }

        return this;
    }

    apply(romFile: ArrayBuffer) {
        if (this.sourceChecksum !== crc32(romFile)) {
            throw new Error('Source checksum incorrect');
        }

        let newFileSize = 0;
        let seek = this.actionsOffset;
        let romFileView = new Uint8Array(romFile);

        // determine target filesize
        while (seek < (this.file.byteLength - 12)) {
            let data = this.decodeBPS(this.file, seek);
            let action = {
                type: data.number & 3,
                length: (data.number >> 2) + 1,
            };

            seek += data.length;

            newFileSize += action.length;

            switch (action.type) {
                case BPS.ACTION_TARGET_READ:
                    seek += action.length;
                    break;
                case BPS.ACTION_SOURCE_COPY:
                case BPS.ACTION_TARGET_COPY:
                    seek += this.decodeBPS(this.file, seek).length;
                    break;
            }
        }

        let tempFile = new ArrayBuffer(newFileSize);
        let tempFileView = new Uint8Array(tempFile);

        //patch
        let outputOffset = 0;
        let sourceRelativeOffset = 0;
        let targetRelativeOffset = 0;

        seek = this.actionsOffset;

        while (seek < (this.file.byteLength - 12)) {
            let data = this.decodeBPS(this.file, seek);
            let data2;
            let action = {
                type: data.number & 3,
                length: (data.number >> 2) + 1,
            };

            seek += data.length;

            switch (action.type) {
                case BPS.ACTION_SOURCE_READ:
                    for (let i = 0; i < action.length; ++i) {
                        tempFileView[outputOffset + i] = romFileView[outputOffset + i];
                    }
                    outputOffset += action.length;
                    break;
                case BPS.ACTION_TARGET_READ:
                    for (let i = 0; i < action.length; ++i) {
                        tempFileView[outputOffset + i] = this.file[seek + i];
                    }
                    outputOffset += action.length;
                    seek += action.length;
                    break;
                case BPS.ACTION_SOURCE_COPY:
                    data2 = this.decodeBPS(this.file, seek);
                    seek += data2.length;
                    sourceRelativeOffset += ((data2.number & 1) ? -1 : 1) * (data2.number >> 1);
                    while (action.length--) {
                        tempFileView[outputOffset] = romFileView[sourceRelativeOffset];
                        outputOffset++;
                        sourceRelativeOffset++;
                    }
                    break;
                case BPS.ACTION_TARGET_COPY:
                    data2 = this.decodeBPS(this.file, seek);
                    seek += data2.length;
                    targetRelativeOffset += ((data2.number & 1) ? -1 : 1) * (data2.number >> 1);
                    while (action.length--) {
                        tempFileView[outputOffset] = tempFileView[targetRelativeOffset];
                        outputOffset++;
                        targetRelativeOffset++;
                    }
                    break;
            }
        }

        if (this.targetChecksum !== crc32(tempFile)) {
            throw new Error('Target checksum incorrect');
        }

        return tempFile;
    }

    decodeBPS(dataBytes: Uint8Array, i: number) {
        let number = 0;
        let shift = 1;
        let len = 0;
        while (true) {
            let x = dataBytes[i];
            i++;
            len++;
            number += (x & 0x7f) * shift;
            if (x & 0x80) {
                break;
            }
            shift <<= 7;
            number += shift;
        }
        return {
            number: number,
            length: len,
        };
    }
}
