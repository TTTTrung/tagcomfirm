<div>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session()->has('success'))
            <div class="flex items-center p-4 mb-4 text-sm text-green-800 border border-green-300 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400 dark:border-green-800" role="alert">
                <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                </svg>
                <span class="sr-only">Info</span>
                <div>
                  <span class="font-medium">Success alert!</span> {{ session('success') }}
                </div>
              </div>
              @endif
              @if (session()->has('error'))
              <div class="flex items-center p-4 mb-4 text-sm text-yellow-800 border border-yellow-300 rounded-lg bg-yellow-50 dark:bg-gray-800 dark:text-yellow-300 dark:border-yellow-800" role="alert">
                <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                </svg>
                <span class="sr-only">Info</span>
                <div>
                  <span class="font-medium">Warning alert!</span>{{ session('error') }}
                </div>
              </div>
              @endif
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="pb-4 bg-white  flex justify-between">
                        <label for="table-search" class="sr-only">Search</label>
                        <div class="relative mt-1">
                            <div class="absolute inset-y-0 rtl:inset-r-0 start-0 flex items-center ps-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                                </svg>
                            </div>
                            <input type="text"  class="block pt-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 " placeholder="Search for items">
                        </div>
                        <button type="button" wire:click="openCreateModal"  class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2">
                            Create
                        </button>
                    </div>
                    {{-- --------------- --}}
                    <div>
                        @foreach ($plands as $index => $pland)
                        <div wire:key="items-{{ $index }}" class="wrapper">
                            <div x-data="{ isOpen: false }" @click="isOpen = !isOpen" class="tab px-5 py-2 bg-slate-100 shadow-lg relative mb-4 rounded-md cursor-pointer">
                                <div 
                                    class="flex justify-between items-center font-semibold text-lg after:absolute after:right-5 after:text-2xl after:text-gray-400 hover:after:text-gray-950 peer-checked:after:transform peer-checked:after:rotate-45">
                                    <div class="flex">
                                        <h2 class="w-8 h-8 bg-sky-300 text-white flex justify-center items-center rounded-sm mr-3">{{ $index + 1 }}</h2>
                                        <h3>{{ $pland->plan_id}}</h3>
                                        <h3 class="ml-1">{{ $pland->company_name }}</h3>
                                    </div>
                                    <div>
                                        <button wire:click.stop="openApproveModal({{ $pland->id }})"  class="text-white bg-green-500 hover:bg-green-700  font-medium rounded-lg text-sm px-3 py-1.5 me-2 mb-2">
                                            Approve
                                            </button>
                                        <button wire:click.stop="openEditModal({{ $pland->id }})"  class="text-white bg-yellow-500 hover:bg-yellow-700  font-medium rounded-lg text-sm px-3 py-1.5 me-2 mb-2">
                                            Edit
                                            </button>
                                        <button wire:click.stop="openDeleteModal({{ $pland->id }})"  class="text-white bg-red-500 hover:bg-red-700  font-medium rounded-lg text-sm px-3 py-1.5 me-2 mb-2">
                                        Delete
                                        </button>
                                    </div>
                                    
                                </div>
                                {{-- Accordion content --}}
                                <div x-show="isOpen" class="answer justify-center mt-5 h-full mr-9">  
                                    <table class="m-6 w-full  text-sm text-left rtl:text-right text-gray-500 ">
                                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                            <tr>
                                                <th scope="col" class="px-6 py-3">
                                                    No.
                                                </th>
                                                <th scope="col" class="px-6 py-3">
                                                    Due date
                                                </th>
                                                <th scope="col" class="px-6 py-3">
                                                    Issue/serial/lot/line
                                                </th>
                                                <th scope="col" class="px-6 py-3">
                                                    PO.
                                                </th>
                                                <th scope="col" class="px-6 py-3">
                                                    Outside part
                                                </th>
                                                <th scope="col" class="px-6 py-3">
                                                    Quantity 
                                                </th> 
                                                <th scope="col" class="px-6 py-3">
                                                    Price 
                                                </th> 
                                                <th scope="col" class="px-6 py-3">
                                                    Body
                                                </th>
                                                <th scope="col" class="px-6 py-3">
                                                    Ship To
                                                </th>                                             
                                            </tr>
                                        </thead>
                                        @foreach ($pland->listitems as $listitem)
                                            <tr class="bg-white border-b hover:bg-gray-50">
                                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap ">
                                                    {{ $loop->iteration }}
                                                </th>
                                                
                                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                                    {{ $pland->duedate }}
                                                </th>
                                                <td class="px-6 py-4">
                                                    {{ $listitem->issue }}
                                                </td>
                                                <td class="px-6 py-4">
                                                    {{ $listitem->po }}
                                                </td>
                                                <td class="px-6 py-4">
                                                    {{ $listitem->outpart }}
                                                </td>
                                                <td class="px-6 py-4">
                                                    {{$listitem->quantity }}
                                                </td>
                                                <td class="px-6 py-4">
                                                    {{$listitem->prize }}
                                                </td>
                                                <td class="px-6 py-4">
                                                    {{ $listitem->body }}
                                                </td>
                                                <td class="px-6 py-4">
                                                    {{ $listitem->ship_to }}
                                                </td>
                                                {{-- <td class="px-6 py-4">
                                                    {{ optional($part->createdBy)->name }}
                                                </td>
                                                <td class="px-6 py-4">
                                                    {{ optional($part->updatedBy)->name }}
                                                </td> --}}
                                                {{-- <td class="px-6 py-4">
                                                    <button wire:click="openEditModal({{ $part->id }})"  class="font-medium text-white rounded-lg bg-yellow-400 hover:bg-yellow-500 focus:ring-4 focus:ring-red-300 px-2 py-0.5">Edit</button>
                                                  
                                                </td> --}}
                                            </tr>   
                                        @endforeach
                                    </table>
                                </div>
                                {{-- End Accordion content --}}
                            </div>    
                        </div>
                    @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Create modal --}}
    @if($showCreateModal)
    <div class="fixed inset-0 bg-gray-300 opacity-40"  wire:click="hideCreateModal"></div>
    <form wire:submit.prevent="createPlan" class="flex flex-col justify-between bg-white rounded m-auto fixed inset-0" :style="{ 'max-height': '800px', 'max-width' : '1400px' }">
        <div class="bg-blue-700 text-white w-full px-4 py-3 flex items-center justify-between border-b border-gray-300">
            <div class="text-xl font-bold">Create Modal</div>
            <button wire:click="hideCreateModal" type="button" class="focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div class="bg-gray-100 w-full flex justify-between p-4">
            <div class="flex">
                <div class="w-48">
                    <label for="due" class="text-xs">Due Date</label>
                    <input id="due" wire:model="duedate" type="datetime-local" class="w-full p-2 border border-gray-300 text-xs rounded" required /> 
                    @error('duedate') 
                        <span class="text-red-500 text-xs">{{ $message }}</span> 
                    @enderror 
                </div>
                <div class="w-48 ml-5">
                    <label for="car" class="text-xs">Car</label>
                        <label class="w-40 text-sm font-medium text-gray-900"></label>
                        <select id= "car" wire:model="car"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 ">
                            <option value="">All</option>
                            <option value="4W">4W</option>    
                            <option value="6W">6W</option>    
                            <option value="Trailer">Trailer</option>    
                            <option value="Station">Station</option>     
                        </select>
                    @error('car') 
                    <span class="text-red-500 text-xs">{{ $message }}</span> 
                    @enderror 
                </div>   
            </div>
        </div> 
        <div class="flex-grow bg-white w-full flex flex-col items-center justify-start overflow-y-auto">
            <div>
                @if($duplicateInput)
                <h3 class="text-red-500 text-xs">There is a duplicate input value in issue field.</h3>
                @endif
                <table class="mt-4 max-w-8xl text-sm text-left rtl:text-right text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3">Customer Id.</th>
                            <th scope="col" class="px-6 py-3">Issue/serial/lot/line</th>
                            <th scope="col" class="px-6 py-3">PO.</th>
                            <th scope="col" class="px-6 py-3">Outside part No.</th>
                            <th scope="col" class="px-6 py-3">Quantity</th>
                            <th scope="col" class="px-6 py-3">Body</th>
                            <th scope="col" class="px-6 py-3">Ship To</th>
                            <th scope="col" class="px-6 py-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($itemDetails as $rowIndex => $row)
                        <tr>
                            <td class="px-6" >
                                @error('itemDetails.' . $rowIndex . '.customer') 
                                    <span class="text-red-500 text-xs">{{ $message }}</span> 
                                @enderror 
                            </td>
                            <td class="px-6" >
                                @error('itemDetails.' . $rowIndex . '.issue') 
                                    <span class="text-red-500 text-xs">{{ $message }}</span> 
                                @enderror 
                            </td>
                            <td class="px-6" >
                                @error('itemDetails.' . $rowIndex . '.po') 
                                    <span class="text-red-500 text-xs">{{ $message }}</span> 
                                @enderror 
                            </td>
                            <td class="px-6" >
                                @error('itemDetails.' . $rowIndex . '.outpart') 
                                    <span class="text-red-500 text-xs">{{ $message }}</span> 
                                @enderror 
                            </td>
                            <td class="px-6" >
                                @error('itemDetails.' . $rowIndex . '.quantity') 
                                    <span class="text-red-500 text-xs">{{ $message }}</span> 
                                @enderror 
                            </td>
                            <td class="px-6" >
                                @error('itemDetails.' . $rowIndex . '.body') 
                                    <span class="text-red-500 text-xs">{{ $message }}</span> 
                                @enderror 
                            </td>
                            <td class="px-6" >
                                @error('itemDetails.' . $rowIndex . '.ship_to') 
                                    <span class="text-red-500 text-xs">{{ $message }}</span> 
                                @enderror 
                            </td>
                        </tr>
                            <tr class="bg-white border-b hover:bg-gray-50">
                                    <td scope="row" class="px-6 py-4 font-medium text-gray-900 ">
                                        <input wire:model="itemDetails.{{ $rowIndex }}.customer" type="text" class="w-full p-2 border border-gray-300 rounded text-sm"  />
                                    </td>
                                   
                                    <td scope="row" class="px-6 py-4 font-medium text-gray-900 ">
                                        <input wire:model="itemDetails.{{ $rowIndex }}.issue" type="text" class="w-full p-2 border border-gray-300 text-sm rounded"  />
                                    </td>
                                    <td scope="row" class="px-6 py-4 font-medium text-gray-900 ">
                                        <input wire:model="itemDetails.{{ $rowIndex }}.po" type="text" class="w-full p-2 border border-gray-300 text-sm rounded"  />
                                    </td>
                                    <td scope="row" class="px-6 py-4 font-medium text-gray-900 ">
                                        <input wire:model="itemDetails.{{ $rowIndex }}.outpart" type="text" class="w-full p-2 border border-gray-300 text-sm rounded" required />
                                    </td>
                                    <td scope="row" class="px-6 py-4 font-medium text-gray-900 ">
                                        <input wire:model="itemDetails.{{ $rowIndex }}.quantity" type="text" class="w-full p-2 border border-gray-300 text-sm rounded" required />     
                                    </td>
                                    <td scope="row" class="px-6 py-4 font-medium text-gray-900 ">
                                        <input wire:model="itemDetails.{{ $rowIndex }}.body" type="text" class="w-full p-2 border border-gray-300 text-sm rounded" />     
                                    </td>
                                    <td scope="row" class="px-6 py-4 font-medium text-gray-900 ">
                                        <input wire:model="itemDetails.{{ $rowIndex }}.ship_to" type="text" class="w-full p-2 border border-gray-300 text-sm rounded" />     
                                    </td>
                                
                                <td class="px-6 py-4">
                                    <button type="button" wire:click="removeItem({{ $rowIndex }})" class="text-red-500 focus:outline-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                           
                        @endforeach
                    </tbody>
                </table>
            </div>      
        </div>
    <button type="button" wire:click="addItem" class="mt-2 text-white bg-green-500 hover:bg-green-600 font-medium rounded-lg text-sm px-4 py-2">Add Item</button>   
    <div class="bg-gray-100 w-full flex justify-between p-4">
        <div class="flex">
        <button type="button" wire:click="clearItem" class="text-white bg-slate-700 hover:bg-slate-800 focus:ring-4 focus:outline-none focus:ring-slate-300 font-medium rounded-lg text-sm px-5 py-2.5">Clear</button>
            <form>   
                <input class="ml-2 block w-full text-sm text-slate-500
                file:mr-4 file:py-2.5 file:px-5 file:rounded-md
                file:border-0 file:text-sm file:font-semibold
                file:bg-pink-200 file:text-pink-700
                hover:file:bg-pink-300" type="file" wire:model="csvFile">
                <button type="button" wire:click="processCsv" class="text-white bg-slate-700 hover:bg-slate-800 focus:ring-4 focus:outline-none focus:ring-slate-300 font-medium rounded-lg text-sm px-5 py-2.5">Import</button>
            </form> 
            <button type="button" wire:click="checkQuantity" class="ml-2 text-white bg-orange-400 hover:bg-orange-500 focus:ring-4 focus:outline-none focus:ring-orange-200 font-medium rounded-lg text-sm px-5 py-2.5">Check</button>
        </div>
        
        <div>
        <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5">Create Plan</button>
        </div>
    </div>
</form>
</div>
@endif 




{{-- delete mdal --}}
@if($showDeleteModal)
<div class="fixed inset-0 p-4 flex flex-wrap justify-center items-center w-full h-full z-[1000] before:fixed before:inset-0 before:w-full before:h-full before:bg-[rgba(0,0,0,0.5)] overflow-auto font-[sans-serif]">
 <div class="fixed inset-0 bg-gray-300 opacity-40" wire:click="hideDeleteModal"></div>
   <div class="w-full max-w-md bg-white shadow-lg rounded-md p-6 relative">
     <svg wire:click="hideDeleteModal" xmlns="http://www.w3.org/2000/svg"
       class="w-3.5 cursor-pointer shrink-0 fill-black hover:fill-red-500 float-right" viewBox="0 0 320.591 320.591">
       <path
         d="M30.391 318.583a30.37 30.37 0 0 1-21.56-7.288c-11.774-11.844-11.774-30.973 0-42.817L266.643 10.665c12.246-11.459 31.462-10.822 42.921 1.424 10.362 11.074 10.966 28.095 1.414 39.875L51.647 311.295a30.366 30.366 0 0 1-21.256 7.288z"
         data-original="#000000"></path>
       <path
         d="M287.9 318.583a30.37 30.37 0 0 1-21.257-8.806L8.83 51.963C-2.078 39.225-.595 20.055 12.143 9.146c11.369-9.736 28.136-9.736 39.504 0l259.331 257.813c12.243 11.462 12.876 30.679 1.414 42.922-.456.487-.927.958-1.414 1.414a30.368 30.368 0 0 1-23.078 7.288z"
         data-original="#000000"></path>
     </svg>
     <div class="my-8 text-center">
       <svg xmlns="http://www.w3.org/2000/svg" class="w-16 fill-red-500 inline" viewBox="0 0 24 24">
         <path
           d="M19 7a1 1 0 0 0-1 1v11.191A1.92 1.92 0 0 1 15.99 21H8.01A1.92 1.92 0 0 1 6 19.191V8a1 1 0 0 0-2 0v11.191A3.918 3.918 0 0 0 8.01 23h7.98A3.918 3.918 0 0 0 20 19.191V8a1 1 0 0 0-1-1Zm1-3h-4V2a1 1 0 0 0-1-1H9a1 1 0 0 0-1 1v2H4a1 1 0 0 0 0 2h16a1 1 0 0 0 0-2ZM10 4V3h4v1Z"
           data-original="#000000" />
         <path d="M11 17v-7a1 1 0 0 0-2 0v7a1 1 0 0 0 2 0Zm4 0v-7a1 1 0 0 0-2 0v7a1 1 0 0 0 2 0Z"
           data-original="#000000" />
       </svg>
       <h4 class="text-xl font-semibold mt-6">Are you sure you want to delete it?</h4>
       {{-- <p class="text-sm text-gray-500 mt-4">คุณต้องการที่จะลบเบอร์โทร {{ $selectedPhoneData['phonenumber'] }} ใช้หรือไม่</p> --}}
     </div>
     <div class="flex flex-col space-y-2">
       <button wire:click="deleteData" type="button"
         class="px-6 py-2.5 rounded-md text-white text-sm font-semibold border-none outline-none bg-red-500 hover:bg-red-600 active:bg-red-500">Delete</button>
       <button wire:click="hideDeleteModal" type="button"
         class="px-6 py-2.5 rounded-md text-black text-sm font-semibold border-none outline-none bg-gray-200 hover:bg-gray-300 active:bg-gray-200">Cancel</button>
     </div>
   </div>
 </div>
 @endif

 {{-- Edit modal --}}

 @if($showEditModal)
    <div class="fixed inset-0 bg-gray-300 opacity-40"  wire:click="hideEditModal"></div>
    <form wire:submit.prevent="editPlan" class="flex flex-col justify-between bg-white rounded m-auto fixed inset-0" :style="{ 'max-height': '800px', 'max-width' : '1400px' }">
        <div class="bg-blue-700 text-white w-full px-4 py-3 flex items-center justify-between border-b border-gray-300">
            <div class="text-xl font-bold">Edit Modal</div>
            <button wire:click="hideEditModal" type="button" class="focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div class="bg-gray-100 w-full flex justify-between p-4">
            <div class="flex">
                <div class="w-48">
                    <label for="due" class="text-xs">Due Date</label>
                    <input id="due" wire:model="eDuedate" type="datetime-local" class="w-full p-2 border border-gray-300 text-xs rounded" required /> 
                    @error('duedate') 
                        <span class="text-red-500 text-xs">{{ $message }}</span> 
                    @enderror 
                </div>
                <div class="w-48 ml-5">
                    <label for="car" class="text-xs">Car</label>
                        <label class="w-40 text-sm font-medium text-gray-900"></label>
                        <select id= "car" wire:model="eCar"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 ">
                            <option value="">All</option>
                            <option value="4W">4W</option>    
                            <option value="6W">6W</option>    
                            <option value="Trailer">Trailer</option>    
                            <option value="Station">Station</option>    
                        </select>
                    @error('car') 
                    <span class="text-red-500 text-xs">{{ $message }}</span> 
                    @enderror 
                </div>   
            </div>
        </div>
        <div class="flex-grow bg-white w-full flex flex-col items-center justify-start overflow-y-auto">        
            <div>
            @if($duplicateInput)
                <h3 class="text-red-500 text-xs">There is a duplicate input value in issue field.</h3>
            @endif
                <table class="mt-4 max-w-7xl text-sm text-left rtl:text-right text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            
                            <th scope="col" class="px-6 py-3">Customer</th>
                            <th scope="col" class="px-6 py-3">Issue/serial/lot/line</th>
                            <th scope="col" class="px-6 py-3">PO.</th>
                            <th scope="col" class="px-6 py-3">Outside part No.</th>
                            <th scope="col" class="px-6 py-3">Quantity</th>
                            <th scope="col" class="px-6 py-3">Price</th>
                            <th scope="col" class="px-6 py-3">Body</th>
                            <th scope="col" class="px-6 py-3">Ship To</th>
                            <th scope="col" class="px-6 py-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($editItemDetails as $rowIndex => $row)
                        <tr>
                            <td class="px-6" >
                                @error('editItemDetails.' . $rowIndex . '.customer') 
                                    <span class="text-red-500 text-xs">{{ $message }}</span> 
                                @enderror 
                            </td>
                            <td class="px-6" >
                                @error('editItemDetails.' . $rowIndex . '.issue') 
                                    <span class="text-red-500 text-xs">{{ $message }}</span> 
                                @enderror 
                            </td>
                            <td class="px-6" >
                                @error('editItemDetails.' . $rowIndex . '.po') 
                                    <span class="text-red-500 text-xs">{{ $message }}</span> 
                                @enderror 
                            </td>
                            <td class="px-6" >
                                @error('editItemDetails.' . $rowIndex . '.outpart') 
                                    <span class="text-red-500 text-xs">{{ $message }}</span> 
                                @enderror 
                            </td>
                            <td class="px-6" >
                                @error('editItemDetails.' . $rowIndex . '.quantity') 
                                    <span class="text-red-500 text-xs">{{ $message }}</span> 
                                @enderror 
                            </td>
                            <td class="px-6" >
                                @error('editItemDetails.' . $rowIndex . '.prize') 
                                    <span class="text-red-500 text-xs">{{ $message }}</span> 
                                @enderror 
                            </td>
                            <td class="px-6" >
                                @error('editItemDetails.' . $rowIndex . '.body') 
                                    <span class="text-red-500 text-xs">{{ $message }}</span> 
                                @enderror 
                            </td>
                            <td class="px-6" >
                                @error('editItemDetails.' . $rowIndex . '.ship_to') 
                                    <span class="text-red-500 text-xs">{{ $message }}</span> 
                                @enderror 
                            </td>
                        </tr>
                            <tr class="bg-white border-b hover:bg-gray-50">                                
                        
                                    <td scope="row" class="px-6 py-4 font-medium text-gray-900 ">
                                        <input wire:model="editItemDetails.{{ $rowIndex }}.customer" type="text" class="w-full p-2 border border-gray-300 rounded text-sm" required />
                                    </td>
                                    <td scope="row" class="px-6 py-4 font-medium text-gray-900 ">
                                        <input wire:model="editItemDetails.{{ $rowIndex }}.issue" type="text" class="w-full p-2 border border-gray-300 rounded text-sm" required />
                                    </td>
                                    <td scope="row" class="px-6 py-4 font-medium text-gray-900 ">
                                        <input wire:model="editItemDetails.{{ $rowIndex }}.po" type="text" class="w-full p-2 border border-gray-300 rounded text-sm" required />
                                    </td>
                                    <td scope="row" class="px-6 py-4 font-medium text-gray-900 ">
                                        <input wire:model="editItemDetails.{{ $rowIndex }}.outpart" type="text" class="w-full p-2 border border-gray-300 rounded text-sm" required />
                                    </td>
                                    <td scope="row" class="px-6 py-4 font-medium text-gray-900 ">
                                        <input wire:model="editItemDetails.{{ $rowIndex }}.quantity" type="text" class="w-full p-2 border border-gray-300 rounded text-sm" required />     
                                    </td>
                                    <td scope="row" class="px-6 py-4 font-medium text-gray-900 ">
                                        <input wire:model="editItemDetails.{{ $rowIndex }}.prize" type="text" class="w-full p-2 border border-gray-300 rounded text-sm" required />     
                                    </td>
                                    <td scope="row" class="px-6 py-4 font-medium text-gray-900 ">
                                        <input wire:model="editItemDetails.{{ $rowIndex }}.body" type="text" class="w-full p-2 border border-gray-300 rounded text-sm" />     
                                    </td>
                                    <td scope="row" class="px-6 py-4 font-medium text-gray-900 ">
                                        <input wire:model="editItemDetails.{{ $rowIndex }}.ship_to" type="text" class="w-full p-2 border border-gray-300 rounded text-sm" required />     
                                    </td>                                  
                                <td class="px-6 py-4">
                                    <button type="button" wire:click="editRemove({{ $rowIndex }})" class="text-red-500 focus:outline-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>                           
                        @endforeach
                    </tbody>
                </table>
            </div>      
        </div>
        <button type="button" wire:click="editAdd" class="mt-2 text-white bg-green-500 hover:bg-green-600 font-medium rounded-lg text-sm px-4 py-2">Add Item</button>    
    <div class="bg-gray-100 w-full flex justify-end p-4">        
        <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5">Edit Plan</button>
    </div>
</form>
</div>
@endif 



{{-- approve mdal --}}
@if($approveModal)
<div class="fixed inset-0 p-4 flex flex-wrap justify-center items-center w-full h-full z-[1000] before:fixed before:inset-0 before:w-full before:h-full before:bg-[rgba(0,0,0,0.5)] overflow-auto font-[sans-serif]">
 <div class="fixed inset-0  bg-gray-300 opacity-40" wire:click="hideApproveModal"></div>
   <div class="w-full  max-w-md bg-white shadow-lg rounded-md p-6 relative">
     <svg wire:click="hideApproveModal" xmlns="http://www.w3.org/2000/svg"
       class="w-3.5 cursor-pointer shrink-0 fill-black hover:fill-red-500 float-right" viewBox="0 0 320.591 320.591">
       <path
         d="M30.391 318.583a30.37 30.37 0 0 1-21.56-7.288c-11.774-11.844-11.774-30.973 0-42.817L266.643 10.665c12.246-11.459 31.462-10.822 42.921 1.424 10.362 11.074 10.966 28.095 1.414 39.875L51.647 311.295a30.366 30.366 0 0 1-21.256 7.288z"
         data-original="#000000"></path>
       <path
         d="M287.9 318.583a30.37 30.37 0 0 1-21.257-8.806L8.83 51.963C-2.078 39.225-.595 20.055 12.143 9.146c11.369-9.736 28.136-9.736 39.504 0l259.331 257.813c12.243 11.462 12.876 30.679 1.414 42.922-.456.487-.927.958-1.414 1.414a30.368 30.368 0 0 1-23.078 7.288z"
         data-original="#000000"></path>
     </svg>
     <div class="my-8 text-center justify-center flex items-center">
        <svg width="196px" height="196px" viewBox="0 0 24.00 24.00" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="0.192"></g><g id="SVGRepo_iconCarrier"> <circle opacity="0.5" cx="12" cy="12" r="10" stroke="#4ade80" stroke-width="1.848"></circle> <path d="M8.5 12.5L10.5 14.5L15.5 9.5" stroke="#4ade80" stroke-width="1.848" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
     </div>
     <h4 class="text-xl text-center font-semibold mt-6">Are you sure you want to Approve ?</h4>
     <div class="flex flex-col space-y-2">
       <button wire:click="approvePlan" type="button"
         class="px-6 py-2.5 rounded-md text-white text-sm font-semibold border-none outline-none bg-green-500 hover:bg-green-600 active:bg-green-500">Approve</button>
       <button wire:click="hideApproveModal" type="button"
         class="px-6 py-2.5 rounded-md text-black text-sm font-semibold border-none outline-none bg-gray-200 hover:bg-gray-300 active:bg-gray-200">Cancel</button>
     </div>
   </div>
 </div>
 @endif


</div>
