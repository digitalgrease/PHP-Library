<?php

/*
 * Copyright (c) 2015 Digital Grease Limited.
 */

namespace GreasyLab\Library\Files;

require_once 'FileSystemInterface.php';

use GreasyLab\Library\Files\FileSystemInterface;

/**
 * Provides methods to work with the local file system.
 * 
 * @author Tom Gray
 */
class LocalFileSystem implements FileSystemInterface
{

    /**
     * {@inheritdoc}
     */
    public function filemtime($path)
    {
        return filemtime($path);
    }

    /**
     * {@inheritdoc}
     */
    public function getContent($path)
    {
        return file_get_contents($path);
    }

    /**
     * {@inheritdoc}
     */
    public function getDetailedFileList($path)
    {
        $path = rtrim($path, '/') . '/';
        $fileList = false;
        $files = scandir($path);
        
        if ($files) {
            $files = array_splice($files, 2);
            $fileList = [];
            
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
     * {@inheritdoc}
     */
    public function getFileList($path)
    {
        $fileList = scandir($path);
        if ($fileList) {
            $fileList = array_splice($fileList, 2);
        }
        return $fileList;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function getSize($path)
    {
        return filesize($path);
    }
    
    /**
     * {@inheritdoc}
     */
    public function isDir($path)
    {
        return is_dir($path);
    }

    /**
     * {@inheritdoc}
     */
    public function isFile($path)
    {
        return is_file($path);
    }

    /**
     * {@inheritdoc}
     */
    public function mkdir($path, $mode = -1, $recursive = false)
    {
        return mkdir($path, $mode, $recursive);
    }

    /**
     * {@inheritdoc}
     */
    public function putContent($dest, $data)
    {
        if (file_put_contents($dest, $data)) {
            return true;
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function rmdir($dir)
    {
        foreach ($this->getDirectoryContents($dir) as $file) {
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
     * {@inheritdoc}
     */
    public function touch($path, $time = null, $atime = null)
    {
        return touch($path, $time, $atime);
    }

    /**
     * {@inheritdoc}
     */
    public function unlink($path)
    {
        return unlink($path);
    }
}
