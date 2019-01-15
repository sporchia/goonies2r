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
    const ROOM_LAYOUT_OFFSET = 0x1c010;
    /** @var int */
    const ROOM_DATA_OFFSET = 0x648f;
    /** @var int */
    const ROOM_PPU_OFFSET = 0x7f91;
    /** @var int */
    const MAP_LOCATOR_OFFSET = 0x3ffb0;
    /** @var resource */
    private $rom;
    /** @var string */
    private $tmp_file;
    /** @var array */
    private $write_log = [];

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

        $rom = fopen($this->tmp_file, 'r+');
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
     * Write Locator Device data for map.
     *
     * @param bool $front   front or back of map
     * @param int  $locator which locator device
     * @param int  $address address to write
     *
     * @return $this
     */
    public function updateMapLocator(bool $front, int $locator, int $address) : self
    {
        $write_to = self::MAP_LOCATOR_OFFSET + ($front ? 0x00 : 0x18) + ($locator * 4);

        $this->write($write_to, pack('n', $address));

        return $this;
    }

    /**
     * clear all rooms.
     *
     * @todo this currently skips NPC's
     *
     * @return $this
     */
    public function clearItemsFromRooms() : self
    {
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
