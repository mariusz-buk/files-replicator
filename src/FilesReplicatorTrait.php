<?php

/**
 * Trait FilesReplicator
 *
 * (c) Mariusz Buk 2018 <mariusz@buk.email>
 */

namespace FilesReplicator;

use October\Rain\Database\Collection;
use System\Models\File;

trait FilesReplicatorTrait
{
    private $filesReplicatorDebug = false;

    public function replicate(array $except = null)
    {
        $newObject = parent::replicate($except);

        // we must save the object as object ID is required to attach files
        $newObject->save();

        if (is_array($this->attachOne)) {
            $this->replicateAttachOne($newObject);
        }

        if (is_array($this->attachMany)) {
            $this->replicateAttachMany($newObject);
        }

        return $newObject;
    }

    public function replicateAttachOne($newObject)
    {
        if ($this->filesReplicatorDebug) {
            echo "Started replicating attachOne\n";
        }

        /**
         * We assume file belongs to File class but it can be something else - inherited from File class
         *
         * @var File $file
         */
        foreach ($this->attachOne as $file => $fileClass) {
            if (!$this->$file) {
                continue;
            }

            if ($this->filesReplicatorDebug) {
                echo "Replicating attachOne '" . $file . "'\n";
            }

            /**
             * We create a new File object
             *
             * @var File $newFile
             */
            $newFile = new $fileClass();

            // we use DIRECTORY_SEPERATOR to make sure we are OS independant.
            $newFile = $newFile->fromFile(base_path().
                DIRECTORY_SEPARATOR.
                'storage'.
                DIRECTORY_SEPARATOR.
                'app'.
                DIRECTORY_SEPARATOR.
                $this->$file->getDiskPath()
            );

            $newFile->is_public = true;

            // Copy over the original uploaded file name
            $newFile->file_name = $this->$file->file_name;

            // Copy over the custom title if that was set
            $newFile->title = $this->$file->title;

            // Copy over the custom description if that was set
            $newFile->description = $this->$file->description;

            // attach file
            $newObject->$file()->setSimpleValue($newFile);
            $newObject->save();
        }
    }

    public function replicateAttachMany($newObject)
    {
        if ($this->filesReplicatorDebug) {
            echo "Started replicating attachMany\n";
        }

        foreach ($this->attachMany as $files => $fileClass) {
            if (!($this->$files instanceof Collection)) {
                continue;
            }

            /**
             * We assume file belongs to File class but it can be something else - inherited from File class
             *
             * @var File $file
             */
            foreach ($this->$files as $file) {
                if (!$file) {
                    continue;
                }

                if ($this->filesReplicatorDebug) {
                    echo "Replicating attachMany '" . $files . "'\n";
                }

                /**
                 * We create a new File object
                 *
                 * @var File $newFile
                 */
                $newFile = new $fileClass();

                // we use DIRECTORY_SEPERATOR to make sure we are OS independant.
                $newFile = $newFile->fromFile(base_path() .
                    DIRECTORY_SEPARATOR .
                    'storage' .
                    DIRECTORY_SEPARATOR .
                    'app' .
                    DIRECTORY_SEPARATOR .
                    $file->getDiskPath()
                );

                $newFile->is_public = true;

                // Copy over the original uploaded file name
                $newFile->file_name = $file->file_name;

                // Copy over the custom title if that was set
                $newFile->title = $file->title;

                // Copy over the custom description if that was set
                $newFile->description = $file->description;

                // attach file
                $newObject->$files()->add($newFile);
                $newObject->save();
            }
        }
    }

    /**
     * Fetch file attachments from specified object and insert into current object.
     *
     * @param \Model $object
     * @author Mariusz Buk
     * @throws \Exception
     */
    public function replicateFilesFrom(\Model $object)
    {
        if (!method_exists($object, 'replicateAttachOne') || !method_exists($object, 'replicateAttachMany') ) {
            throw new \Exception('Model has to use FilesReplicator trait.');
        }

        $object->replicateAttachOne($this);
        $object->replicateAttachMany($this);
    }

    /**
     * @return bool
     */
    public function isFilesReplicatorDebug()
    {
        return $this->filesReplicatorDebug;
    }

    /**
     * @param bool $filesReplicatorDebug
     */
    public function setFilesReplicatorDebug($filesReplicatorDebug)
    {
        $this->filesReplicatorDebug = $filesReplicatorDebug;
    }
}
