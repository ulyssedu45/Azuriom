<?php

namespace Azuriom\Models\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Trait to link an image to a model.
 */
trait HasImage
{
    protected $imageDisk = 'public';

    protected static function bootHasImage()
    {
        static::deleted(function (Model $model) {
            if (! ($model->forceDeleting ?? true)) {
                return;
            }

            $model->deleteImage();
        });
    }

    /**
     * Store the image and associate it with this model.
     *
     * @param  \Illuminate\Http\UploadedFile  $file
     * @param  bool  $save
     */
    public function storeImage(UploadedFile $file, bool $save = false)
    {
        $this->deleteImage();

        $path = basename($file->storePublicly($this->getImagePath(), $this->imageDisk));

        $this->setAttribute($this->getImageKey(), $path);

        if ($save) {
            $this->save();
        }

        return $this->imageUrl();
    }

    /**
     * Delete the image associated with this model.
     *
     * @return bool
     */
    public function deleteImage()
    {
        $key = $this->getImageKey();
        $image = $this->getAttribute($key);

        if ($image === null) {
            return false;
        }

        if (! Storage::disk($this->imageDisk)->delete($this->getImagePath($image))) {
            return false;
        }

        $this->setAttribute($key, null);

        return true;
    }

    /**
     * Return true if this this model has an image.
     *
     * @return bool
     */
    public function hasImage()
    {
        return $this->getAttribute($this->getImageKey()) !== null;
    }

    /**
     * Get this post image url.
     *
     * @return string|null
     */
    public function imageUrl()
    {
        $image = $this->getAttribute($this->getImageKey());

        if ($image === null) {
            return null;
        }

        return url(Storage::disk($this->imageDisk)->url($this->getImagePath($image)));
    }

    protected function getImageKey()
    {
        return $this->imageKey ?? 'image';
    }

    protected function getImagePath(string $path = '')
    {
        return ($this->imagePath ?? Str::snake(Str::pluralStudly(class_basename($this)))).'/'.$path;
    }
}
