<div>  
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between align-middle">
                <div class="relative mt-1">
                    <div class="absolute inset-y-0 rtl:inset-r-0 start-0 flex items-center ps-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                        </svg>
                    </div>
                <input type="text" wire:model.live="searchPart" class="block pt-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500" placeholder="Search for items">
             </div>
             <button type="button" wire:click="openCreateImage"  class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2">
                Create
            </button>
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-3">
                                    No.
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Part Name 
                                </th>
                                <th scope="col" class="px-6 py-3">
                                   Image 
                                </th>
                                 <th scope="col" class="px-6 py-3">
                                    Issue 
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($imgs as $img)
                            <tr class="bg-white border-b hover:bg-gray-50">
                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap ">
                                    {{ $loop->iteration }}
                                </th>
                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                    {{ $img->img_part}}
                                </th>
                                
                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                 <img class="object-fill h-48 w-72" src="{{ asset('storage/' . $img->img_path) }}" alt="Uploaded Image">
                                </th>
                                <td class="px-6 py-4">
                                    <button wire:click="openEditImage({{ $img->id }})"  class="font-medium text-white rounded-lg bg-yellow-400 hover:bg-yellow-500 focus:ring-4 focus:ring-red-300 px-2 py-0.5">Edit</button> 
                                </td>
                            </tr>   
                            @endforeach 
                        </tbody>
                    </table>
                        <div class="py-2 px-2">
                            {{ $imgs->onEachSide(3)->links() }}
                        </div>                         
                    </div> 
                </div>
            </div>
        </div>
    </div> 
      {{-- Importmodal --}}
    @if($showCreateImage)
        <div class="fixed inset-0 bg-gray-300 opacity-40"  wire:click="closeCreateImage"></div>
        <form wire:submit.prevent="createImage" class="flex flex-col justify-between bg-white rounded m-auto fixed inset-0" 
        :style="{ 'max-height': '500px', 'max-width' : '600px' }">
            <div class="bg-blue-700 text-white w-full px-4 py-3 flex items-center justify-between border-b border-gray-300">
                <div class="text-xl font-bold">Create master image</div>
                <button wire:click="closeCreateImage" type="button" class="focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="flex-grow bg-white w-full flex flex-col items-center justify-start overflow-y-auto">
              <div class="m-3 ml-5 w-64 ">
                    <label  class="block mb-2 text-sm font-medium text-gray-900 ">Part name</label>
                    <input  wire:model="part" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required />
                    @error('part') 
                        <span class="text-red-500 text-xs">{{ $message }}</span> 
                    @enderror 
                </div> 
              <div class=" mt-6  ">
              <input class="ml-2 block text-sm w-60 text-slate-500
                file:mr-4 file:py-2.5 file:px-5 file:rounded-md
                file:border-0 file:text-sm file:font-semibold
                file:bg-pink-200 file:text-pink-700
                hover:file:bg-pink-300" type="file" wire:model="imgPath">  
                @error('imgPath') 
                    <span class="text-red-500 text-xs">{{ $message }}</span> 
                @enderror 
                </div>
                <div class="mt-2">
                    @if($imgPath)
                        <img class="w-44 h-44 rounded-md"src="{{ $imgPath->temporaryUrl() }}" alt="">
                    @endif
                </div>
            </div>
            <div class="bg-gray-100 w-full flex justify-between p-4">
                <div class="flex">
                </div>
                <div>
                 <button type="submit" 
        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5">save</button>
                </div>
            </div>
        </form>
        </div>
    @endif 
    @if($showEditImage)
        <div class="fixed inset-0 bg-gray-300 opacity-40"  wire:click="closeEditImage"></div>
        <form wire:submit.prevent="editImage" class="flex flex-col justify-between bg-white rounded m-auto fixed inset-0" 
        :style="{ 'max-height': '400px', 'max-width' : '500px' }">
        <div class="bg-yellow-500 text-white w-full px-4 py-3 flex items-center justify-between border-b border-gray-300">
                <div class="text-xl font-bold">Create master image</div>
                <button wire:click="closeEditImage" type="button" class="focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="flex-grow bg-white w-full flex flex-col items-center justify-start overflow-y-auto">
              <div class="m-3 ml-5 w-64 ">
                    <label  class="block mb-2 text-sm font-medium text-gray-900 ">Part name</label>
                    <input  wire:model="part" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required />
                    @error('part') 
                        <span class="text-red-500 text-xs">{{ $message }}</span> 
                    @enderror 
                </div> 
              <div class=" mt-6  ">
              <input class="ml-2 block text-sm w-60 text-slate-500
                file:mr-4 file:py-2.5 file:px-5 file:rounded-md
                file:border-0 file:text-sm file:font-semibold
                file:bg-pink-200 file:text-pink-700
                hover:file:bg-pink-300" type="file" wire:model="imgPath">  
                @error('imgPath') 
                    <span class="text-red-500 text-xs">{{ $message }}</span> 
                @enderror 
                </div>
                 <div class="mt-2">
                    @if($imgPath)
                        <img class="w-44 h-44 rounded-md"src="{{ $imgPath->temporaryUrl() }}" alt="">
                    @endif
                </div>
            </div>
            <div class="bg-gray-100 w-full flex justify-between p-4">
                <div class="flex">
                </div>
                <div>
                 <button type="submit" 
                    class="text-white bg-yellow-400 hover:bg-yellow-500 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5">save</button>
                </div>
            </div>
        </form>
        </div>
    @endif 
</div>
