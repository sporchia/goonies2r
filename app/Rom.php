<?php

namespace App;

use Illuminate\Support\Facades\Log;

/**
 * Wrapper for ROM file.
 */
class Rom
{
    /** @var string */
    const ORGINAL_MD5 = 'd38325cffb9ba2e6f57897c0e9564cc0';
    /** @var int */
    const ROOM_LAYOUT_OFFSET = 0x1f26b;
    /** @var int */
    const ROOM_LAYOUTS = 0x6c70;
    /** @var int */
    const ROOM_NEW_LAYOUTS = 0x7eea;
    /** @var int */
    const ROOM_DATA_OFFSET = 0x648f;
    /** @var resource */
    private $rom;
    /** @var string */
    private $tmp_file;
    /** @var array */
    private $write_log = [];
    /** @var array */
    protected $remodel_data = [
        0x0c => [[0x18, 0x19], [0x1c, 0x1d]],
        0x0d => [[0x3c, 0x3d], [0x40, 0x41]],
        0x0e => [[0x66, 0x67], [0x68, 0x69]],
        0x0f => [[0x8a, 0x8b], [0x8e, 0x8f]],
     ];
    /** @var array */
    protected $remodel_blank_data = [
        0x0c => [[0x10, 0x11], [0x14, 0x15]],
        0x0d => [[0x34, 0x35], [0x38, 0x39]],
        0x0e => [[0x5b, 0x5c], [0x5f, 0x60]],
        0x0f => [[0x6f, 0x70], [0x73, 0x74]],
     ];

    /**
     * Create a new wrapper.
     *
     * @param string $source_location location of source ROM to edit
     *
     * @throws \Exception if ROM source isn't readable
     *
     * @return void
     */
    public function __construct(string $source_location = null)
    {
        if ($source_location !== null && !is_readable($source_location)) {
            throw new \Exception('Source ROM not readable');
        }

        $tmp_file = tempnam(sys_get_temp_dir(), __CLASS__);
        if ($tmp_file === false) {
            // @codeCoverageIgnoreStart
            throw new \Exception('Unable to create tmp file');
            // @codeCoverageIgnoreEnd
        }
        $this->tmp_file = $tmp_file;

        if ($source_location !== null) {
            copy($source_location, $this->tmp_file);
        }

        $rom = fopen($this->tmp_file, "r+");
        if ($rom === false) {
            // @codeCoverageIgnoreStart
            throw new \Exception('Unable to open tmp file');
            // @codeCoverageIgnoreEnd
        }
        $this->rom = $rom;
    }

    /**
     * Get MD5 of current file.
     *
     * @return string
     */
    public function getMD5() : string
    {
        return hash_file('md5', $this->tmp_file);
    }

    /**
     * Save the changes to this output file.
     *
     * @param string $output_location location on the filesystem to write the new ROM.
     *
     * @return bool
     */
    public function save(string $output_location) : bool
    {
        return copy($this->tmp_file, $output_location);
    }

    /**
     * Write packed data at the given offset.
     *
     * @param int $offset location in the ROM to begin writing
     * @param string $data data to write to the ROM
     * @param bool $log write this write to the log
     *
     * @return $this
     */
    public function write(int $offset, string $data, bool $log = true) : self
    {
        if ($log) {
            $unpacked = array_values(unpack('C*', $data));
            $this->write_log[] = [$offset => $unpacked];
        }
        fseek($this->rom, $offset);
        fwrite($this->rom, $data);

        return $this;
    }

    /**
     * Get the array of bytes written in the order they were written to the rom.
     *
     * @return array
     */
    public function getWriteLog() : array
    {
        return $this->write_log;
    }

    /**
     * Read data from the ROM file into an array.
     *
     * @param int $offset location in the ROM to begin reading
     * @param int $length data to read
     * // TODO: this should probably always be an array, or a new Bytes object
     *
     * @return array
     */
    public function read(int $offset, int $length = 1) : array
    {
        fseek($this->rom, $offset);
        $data = fread($this->rom, $length);

        // something really bad has happened, not sure how to force this for testing
        if ($data === false) {
            // @codeCoverageIgnoreStart
            return [];
            // @codeCoverageIgnoreEnd
        }

        $unpacked = unpack('C*', $data);

        return count($unpacked) == 1 ? [$unpacked[1]] : array_values($unpacked);
    }

    /**
     * remodel a Room for a goonie.
     *
     * @param int $old_id   room id to clone
     * @param int $offset   which new offset to use in rom
     * @param int $palette  palette of room to load correct remodel
     *
     * @throws \Exception if reading from rom has incorrect data
     *
     * @return $this
     */
    public function cloneRoomWithCage(int $old_id, int $offset, int $palette): self
    {
        $layout_pointer = self::ROOM_LAYOUT_OFFSET + $old_id * 2;

        $old_address = $this->read($layout_pointer, 2);

        if (count($old_address) !== 2) {
            throw new \Exception('Read from rom did not have full address');
        }

        $old_offset = (int) (($old_address[1] << 8) + $old_address[0] - 0x3ff0);

        $layout = $this->read($old_offset, 16);

        array_splice($layout, 5, 2, $this->remodel_data[$palette][0]);
        array_splice($layout, 9, 2, $this->remodel_data[$palette][1]);

        $new_address = self::ROOM_NEW_LAYOUTS + $offset * 16;
        $this->write($new_address, pack('C*', ...$layout));

        $this->write($layout_pointer, pack('S', $new_address + 0x3ff0));

        return $this;
    }

    /**
     * clear cages from layouts that could have had cages.
     *
     * @return void
     */
    public function remodelOldCageRooms() : void
    {
        $goonie_room_data = [
            0x09 => 0x0c,
            0x19 => 0x0d,
            0x36 => 0x0e,
            0x4e => 0x0f,
            0x64 => 0x0f,
            0x76 => 0x0e,
        ];

        foreach ($goonie_room_data as $room_id => $palette) {
            $layout_pointer = self::ROOM_LAYOUT_OFFSET + $room_id * 2;

            $address = $this->read($layout_pointer, 2);

            if (count($address) !== 2) {
                throw new \Exception('Read from rom did not have full address');
            }

            $layout_offset = (int) (($address[1] << 8) + $address[0] - 0x3ff0);

            $layout = $this->read($layout_offset, 16);

            // remove cage from old room
            array_splice($layout, 5, 2, $this->remodel_blank_data[$palette][0]);
            array_splice($layout, 9, 2, $this->remodel_blank_data[$palette][1]);
            $this->write($layout_offset, pack('C*', ...$layout));
        }
    }

    /**
     * clear all rooms.
     * @TODO: this currently skips NPC's
     *
     * @return $this
     */
    public function clearItemsFromRooms() : self
    {
        $this->remodelOldCageRooms();

        for ($i = 0; $i < 127; ++$i) {
            $room_offset = $i * 4 + 0x648f;
            $item_byte = array_first($this->read($room_offset + 3));
            if ($item_byte != 0x00 && ($item_byte < 0x80 || $item_byte > 0x8f)) {
                Log::debug(sprintf('Clearing Room: %02x Byte: %02x', $i, $item_byte));
                $this->write($room_offset + 3, pack('C', 0x00));
                $this->write($room_offset + 1, pack('C', 0x00));
            }
        }

        return $this;
    }

    /**
     * Object destruction magic method.
     *
     * @return void
     */
    public function __destruct()
    {
        fclose($this->rom);
        unlink($this->tmp_file);
    }
}
