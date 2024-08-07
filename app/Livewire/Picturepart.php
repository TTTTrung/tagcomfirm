<?php

namespace App\Livewire;

use App\Models\PartImage;
use Illuminate\Validation\Rule;
use Intervention\Image\ImageManager;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Picturepart extends Component
{
    use WithFileUploads;
    use WithPagination;
    public $searchPart;
    public $part;
    public $imgPath;
    public $showCreateImage = false;
    public $id;
    public $showEditImage = false;
    public function createImage(){
        $this->validate([
            'part' => ['required','unique:part_image,img_part'],
            'imgPath'=> ['required','image']
        ]);

        $image = ImageManager::imagick()->read($this->imgPath);
        $image->resize(210, 80);

        // Generate a unique name for the image
        $imageName = $this->imgPath->hashName();

            // Save the resized image to storage
        $imagePath = storage_path('app/public/images/' . $imageName);
        $image->save($imagePath); 

        // Store the image information in the database
        PartImage::create([
            'img_part' => $this->part,
            'img_path' => 'images/' . $imageName,
            'created_by' => auth()->id(),
        ]);

        // Close the create image modal or perform any other necessary actions
        $this->closeCreateImage();
    }
    public function openCreateImage(){
        $this->showCreateImage = true;
    }
    public function closeCreateImage(){
        $this->showCreateImage = false;
        $this->reset(['part','imgPath']);
    }
    public function openEditImage($id){
        $this->id = $id;
        $imgae = PartImage::find($id);
        $this->part = $imgae->img_part;
        $this->showEditImage = true;
    }
    public function closeEditImage(){
        $this->showEditImage = false;
        $this->reset(['part','imgPath']);
    }
    public function editImage(){
        $this->validate([
            'part' => ['required', Rule::unique('part_image', 'img_part')->ignore($this->id)],
            'imgPath'=> ['nullable','image']
        ]);

            $updateData = [
            'img_part' => $this->part,
            'updated_by' => auth()->id(),
        ];

        if ($this->imgPath) {
            // Resize the image using Intervention Image
            $image = ImageManager::imagick()->read($this->imgPath);
            $image->resize(210, 80);

            // Generate a unique name for the image
            $imageName = $this->imgPath->hashName();

            // Save the resized image to storage
            $image->save(storage_path('app/public/images/' . $imageName));

            // Add the image path to the update data
            $updateData['img_path'] = 'images/' . $imageName;
        }
       PartImage::where('id', $this->id)->update($updateData); 

        $this->closeEditImage();
    }


    public function render()
    {
        $imgs = PartImage::when($this->searchPart , function($query){
            $query->where('img_part','like','%'.$this->searchPart.'%');
        })->paginate(10);
        return view('livewire.picturepart',compact('imgs'));
    }
}
