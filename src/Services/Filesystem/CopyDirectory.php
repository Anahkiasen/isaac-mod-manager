<?php

namespace Isaac\Services\Filesystem;

use League\Flysystem\Plugin\AbstractPlugin;

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
        $from = str_replace('\\', '/', $from);
        $to = str_replace('\\', '/', $to);

        $contents = $this->filesystem->listContents($from, true);
        foreach ($contents as $file) {
            $destination = str_replace($from, $to, $file['path']);

            if ($file['type'] === 'file') {
                $this->filesystem->forceCopy($file['path'], $destination);
            } else {
                $this->filesystem->createDir($destination);
            }
        }
    }
}
