<div>
    <div class="py-12">
        <div class="mx-auto sm:px-6 lg:px-8" :style="{'max-width' : '90rem' }">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto p-6 text-gray-900">
                    <div class="pb-4 bg-white  flex justify-between">
                        <label for="table-search" class="sr-only">Search</label>
                        <div class="relative mt-1">
                            <div class="absolute inset-y-0 rtl:inset-r-0 start-0 flex items-center ps-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                                </svg>
                            </div>
                            <input type="text"  class="block pt-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500" placeholder="Search for items">
                        </div>
                        <button type="button" wire:click="openCreateModal"  class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 focus:outline-none ">
                            Create
                        </button>
                    </div>
                    <table class="m-6 w-full text-sm text-left rtl:text-right text-gray-500 ">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3">
                                    No.
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Type.
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Part name
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Outside part
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Thairung part 
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    SNP
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Weight
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Created by
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Updated by
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Action
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($parts as $part )
                            <tr class="bg-white border-b hover:bg-gray-50">
                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap ">
                                    {{ $loop->iteration }}
                                </th>
                                
                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                    {{ $part->type}}
                                </th>
                                <td class="px-6 py-4">
                                    {{ $part->partname }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $part->outpart }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $part->trupart }}
                                </td>
                                <td class="px-6 py-4">
                                    {{$part->snp }}
                                </td>
                                <td class="px-6 py-4">
                                    {{$part->weight }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ optional($part->createdBy)->name }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ optional($part->updatedBy)->name }}
                                </td>
                                <td class="px-6 py-4">
                                    <button wire:click="openEditModal({{ $part->id }})"  class="font-medium text-white rounded-lg bg-yellow-400 hover:bg-yellow-500 focus:ring-4 focus:ring-red-300 px-2 py-0.5">Edit</button>
                                    {{-- <button  class="font-medium text-white rounded-lg bg-red-500 hover:bg-red-700 focus:ring-4 focus:ring-red-300 px-2 py-0.5">Delete</button> --}}
                                </td>
                            </tr>   
                            @endforeach 
                        </tbody>
                    </table>
                    <div class="py-2 px-2">
                        {{ $parts->onEachSide(3)->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- createpart modal  --}}
    @if($showCreateModal)
    <div class="fixed inset-0 bg-gray-300 opacity-40"  wire:click="hideCreateModal"></div>
    <form class="flex flex-col justify-between bg-white rounded m-auto fixed inset-0" :style="{ 'max-height': '700px', 'max-width' : '35rem' }" wire:submit.prevent="addPart">
        <div class="bg-blue-700 text-white w-full px-4 py-3 flex items-center justify-between border-b border-gray-300">
            <div class="text-xl font-bold">Create Part</div>
            <button wire:click="hideCreateModal" class="focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
   
        <div class="flex-grow bg-white w-full flex flex-col items-center justify-start overflow-y-auto">
            <!-- Form Content -->
            <div class="w-full">
                
                <div class="mb-5 mt-5 mx-10">
                    <label for="typee" class="block mb-2 text-sm font-medium text-gray-900 ">Type</label>
                    <input id="typee" wire:model="typee" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required />
                    @error('typee') 
                        <span class="text-red-500 text-xs">{{ $message }}</span> 
                    @enderror
                </div>
                    <div class="mb-5  mx-10">
                    <label for="partname" class="block mb-2 text-sm font-medium text-gray-900 ">Part name</label>
                    <input id="partname" wire:model="partname" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required />
                    @error('partname') 
                        <span class="text-red-500 text-xs">{{ $message }}</span> 
                    @enderror
                    </div>
                    <div class="mb-5  mx-10">
                    <label for="outpart" class="block mb-2 text-sm font-medium text-gray-900 ">Outside part</label>
                    <input id="outpart"  wire:model="outpart" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required />
                    @error('outpart') 
                        <span class="text-red-500 text-xs">{{ $message }}</span> 
                    @enderror
                    </div>   
                    <div class="mb-5  mx-10">
                        <label for="trupart" class="block mb-2 text-sm font-medium text-gray-900 ">Thairung part</label>
                        <input id="trupart" wire:model="trupart" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required />
                        @error('trupart') 
                            <span class="text-red-500 text-xs">{{ $message }}</span> 
                        @enderror 
                        </div> 
                    <div class="mb-5  mx-10">
                        <label for="snp" class="block mb-2 text-sm font-medium text-gray-900 ">SNP</label>
                        <input id="snp" wire:model="snp" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required />
                        @error('snp') 
                            <span class="text-red-500 text-xs">{{ $message }}</span> 
                        @enderror        
                    </div>     
                    <div class="mb-5  mx-10">
                        <label for="weight" class="block mb-2 text-sm font-medium text-gray-900">Weight for each part</label>
                        <input id="weight" wire:model="weight" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required />
                        @error('weight') 
                            <span class="text-red-500 text-xs">{{ $message }}</span> 
                        @enderror        
                    </div>     
                </div>
        </div>
    <div class="bg-gray-100 w-full flex justify-end p-4">
        <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5">Add part</button>
    </div>
    </form>
</div>
@endif 
{{-- editmodal --}}
@if($showEditModal)
    <div class="fixed inset-0 bg-gray-300 opacity-40"  wire:click="hideEditModal"></div>
    <form class="flex flex-col justify-between bg-white rounded m-auto fixed inset-0" :style="{ 'max-height': '700px', 'max-width' : '35rem' }" wire:submit.prevent="editPart">
        <div class="bg-yellow-400 text-white w-full px-4 py-3 flex items-center justify-between border-b border-gray-300">
            <div class="text-xl font-bold">Edit Part</div>
            <button wire:click="hideEditModal" class="focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
   
        <div class="flex-grow bg-white w-full flex flex-col items-center justify-start overflow-y-auto">
            <!-- Form Content -->
            <div class="w-full">
                
                <div class="mb-5 mt-5 mx-10">
                    <label for="etypee" class="block mb-2 text-sm font-medium text-gray-900">Type</label>
                    <input id="etypee" wire:model="etypee" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required />
                    @error('etypee') 
                        <span class="text-red-500 text-xs">{{ $message }}</span> 
                    @enderror
                </div>
                    <div class="mb-5  mx-10">
                    <label for="epartname" class="block mb-2 text-sm font-medium text-gray-900 ">Part name</label>
                    <input id="epartname" wire:model="epartname" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 " required />
                    @error('epartname') 
                        <span class="text-red-500 text-xs">{{ $message }}</span> 
                    @enderror
                    </div>
                    <div class="mb-5  mx-10">
                    <label for="eoutpart" class="block mb-2 text-sm font-medium text-gray-900">Outside part</label>
                    <input id="eoutpart"  wire:model="eoutpart" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required />
                    @error('eoutpart') 
                        <span class="text-red-500 text-xs">{{ $message }}</span> 
                    @enderror
                    </div>   
                    <div class="mb-5  mx-10">
                        <label for="etrupart" class="block mb-2 text-sm font-medium text-gray-900 ">Thairung part</label>
                        <input id="etrupart" wire:model="etrupart" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required />
                        @error('etrupart') 
                            <span class="text-red-500 text-xs">{{ $message }}</span> 
                        @enderror 
                        </div> 
                    <div class="mb-5  mx-10">
                        <label for="esnp" class="block mb-2 text-sm font-medium text-gray-900">SNP</label>
                        <input id="esnp" wire:model="esnp" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required />
                        @error('esnp') 
                            <span class="text-red-500 text-xs">{{ $message }}</span> 
                        @enderror        
                    </div>     
                    <div class="mb-5  mx-10">
                        <label for="eweight" class="block mb-2 text-sm font-medium text-gray-900">Weight for each part</label>
                        <input id="eweight" wire:model="eweight" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required />
                        @error('eweight') 
                            <span class="text-red-500 text-xs">{{ $message }}</span> 
                        @enderror        
                    </div>     
                </div>
        </div>
    <div class="bg-gray-100 w-full flex justify-end p-4">
        <button type="submit" class="text-white bg-yellow-400 hover:bg-yellow-500 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5">Add part</button>
    </div>
    </form>
</div>
@endif

</div>
