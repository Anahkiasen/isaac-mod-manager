<?php

namespace Isaac\Services\Filesystem;

use League\Flysystem\Plugin\AbstractPlugin;
use League\Flysystem\Util;

/**
 * Grants the ability to copy a directory in its entirety.
 */
class CopyDirectory extends AbstractPlugin
{
    /**
     * Get the method name.
     *
     * @return string
     */
    public function getMethod()
    {
        return 'copyDirectory';
    }

    /**
     * Copies a directory someplace else.
     *
     * @param string $from
     * @param string $to
     */
    public function handle(string $from, string $to)
    {
        // Unify slashes
        $from = Util::normalizePath($from);
        $to = Util::normalizePath($to);

        $contents = $this->filesystem->listContents($from, true);
        foreach ($contents as $file) {
            $destination = str_replace($from, $to, $file['path']);

            if ($file['type'] === 'file') {
                $this->filesystem->copy($file['path'], $destination);
            } elseif (!$this->filesystem->has($destination)) {
                $this->filesystem->createDir($destination);
            }
        }
    }
}
