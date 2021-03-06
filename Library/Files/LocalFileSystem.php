<?php

/*
 * Copyright (c) 2015 Digital Grease Limited.
 */

namespace DigitalGrease\Library\Files;

require_once 'FileSystemInterface.php';

use DigitalGrease\Library\Files\FileSystemInterface;

/**
 * Provides methods to work with the local file system.
 * 
 * @author Tom Gray
 */
class LocalFileSystem implements FileSystemInterface
{

    /**
     * @inheritDoc
     */
    public function filemtime($path)
    {
        return filemtime($path);
    }

    /**
     * @inheritDoc
     */
    public function getContent($path)
    {
        return file_get_contents($path);
    }

    /**
     * @inheritDoc
     */
    public function getDetailedFileList($path)
    {
        $fileList = [];
        
        $files = $this->getFileList($path);
        if ($files) {
            
            foreach ($files as $filename) {
                $file = $path . $filename;
                
                if (is_file($file)) {
                    $fileList[$filename] = [
                        'type' => FileSystemInterface::REGULAR_FILE,
                        'size' => filesize($file)
                    ];
                } elseif (is_link($file)) {
                    $fileList[$filename] = [
                        'type' => FileSystemInterface::SYMBOLIC_LINK,
                        'size' => filesize($file)
                    ];
                } elseif (is_dir($file)) {
                    $fileList[$filename] = [
                        'type' => FileSystemInterface::DIRECTORY,
                        'size' => 0
                    ];
                } else {
                    throw new \Exception('Unknown file type found.');
                }
                
                $fileList[$filename]['atime'] = fileatime($file);
                $fileList[$filename]['mtime'] = filemtime($file);
                $fileList[$filename]['uid'] = fileowner($file);
                $fileList[$filename]['permissions'] = fileperms($file);
                
                // DO TG LocalFileSystem: Further Development.
//     *      'gid' => (int) ??? Example value = 1000.
//     *      'mode' => (int) ??? Example value = 16832.
            }
        }
        
        return $fileList;
    }

    /**
     * @inheritDoc
     */
    public function getFileList($path)
    {
        $fileList = [];
        
        $path = rtrim($path, '/') . '/';
        
        $files = scandir($path);
        if ($files) {
            $fileList = array_diff($files, ['.', '..']);
        }
        
        return $fileList;
    }

    /**
     * @inheritDoc
     */
    public function getModifiedFiles($directory, $startTime, $endTime)
    {
        $files = [];

        $dirListing = glob($directory . '*', GLOB_MARK);

        foreach ($dirListing as $path) {
            $file['isFile'] = is_file($path);
            $file['path'] = $path;
            $file['timestamp'] = filemtime($path);
            
            if (
                $startTime <= $file['timestamp']
                && $endTime >= $file['timestamp']
            ) {
                $files[] = $file;
            }
            
            if (is_dir($path)) {
                $files = array_merge(
                    $files,
                    FileUtils::getModifiedFiles(
                        $path,
                        $startTime,
                        $endTime
                    )
                );
            }
        }
        return $files;
    }

    /**
     * @inheritDoc
     */
    public function getSize($path)
    {
        return filesize($path);
    }
    
    /**
     * @inheritDoc
     */
    public function isDir($path)
    {
        return is_dir($path);
    }

    /**
     * @inheritDoc
     */
    public function isFile($path)
    {
        return is_file($path);
    }

    /**
     * @inheritDoc
     */
    public function mkdir($path, $mode = 0755, $recursive = false)
    {
        if (is_file($path)) {
            return false;
        }
        return is_dir($path) || mkdir($path, $mode, $recursive);
    }

    /**
     * @inheritDoc
     */
    public function putContent($dest, $data)
    {
        if (file_put_contents($dest, $data)) {
            return true;
        }
        return false;
    }

    /**
     * @inheritDoc
     */
    public function readFromEof($filePath, $marker = PHP_EOL)
    {
        $string = '';
        
        // Open the file at the last character.
        $fp = fopen($filePath, 'r');
        $offset = -1;
        fseek($fp, $offset--, SEEK_END);

        // Remove the trailing newline characters of the file.
        $char = fgetc($fp);
        while ($char == PHP_EOL || $char == '\r') {
            fseek($fp, $offset--, SEEK_END);
            $char = fgetc($fp);
        }

        // Read the characters until the start of the file or the marker.
        while ($char !== false && $char != $marker) {
            $string = $char . $string;
            fseek($fp, $offset--, SEEK_END);
            $char = fgetc($fp);
        }
        
        fclose($fp);
        
        return $string;
    }
    
    /**
     * @inheritDoc
     */
    public function rmdir($dir)
    {
        foreach ($this->getFileList($dir) as $file) {
            $path = rtrim($dir, '/') . '/' . $file;
            if (is_dir($path)) {
                $this->rmdir($path);
            } else {
                unlink($path);
            }
        }
        rmdir($dir);
    }

    /**
     * @inheritDoc
     */
    public function touch($path, $time = null, $atime = null)
    {
        return touch($path, $time, $atime);
    }

    /**
     * @inheritDoc
     */
    public function unlink($path)
    {
        return unlink($path);
    }
}
