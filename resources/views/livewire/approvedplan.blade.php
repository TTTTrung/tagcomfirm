<div>
    <div class="flex items-center justify-between p-4">
    <div class="relative mt-1 mb-2">
        <div class="absolute inset-y-0 rtl:inset-r-0 start-0 flex items-center ps-3 pointer-events-none">
            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
            </svg>
        </div>
        <input type="text" wire:model.live.debounce.300ms="search" class="block pt-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Search for items">
    </div>
    <div class="flex ml-5">
        <div class="flex space-x-1 items-center">
            <label class="w-40 text-sm font-medium text-gray-900"></label>
            <select wire:model.live="company"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 ">
                <option value="">All</option>
                @foreach ($get_com as $company)
                <option value="{{ $company }}">{{ $company }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex space-x-1 items-center">
            <label class="w-40 text-sm font-medium text-gray-900"></label>
            <select wire:model.live="mydata"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 ">
                <option value="">All</option>
                <option value="0">My data</option>
            </select>
        </div>
    </div>
</div>
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
                <div class="flex">
                    <button wire:click.stop="export({{ $pland->id }})"  class="text-white bg-green-500 hover:bg-green-700  font-medium rounded-lg text-sm px-3 py-1.5 me-2 mb-2">
                        Export Tag
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
                                Issue no.
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
                        </tr>
                    </thead>
                    @foreach ($pland->listitems as $listitem)
                        <tr class="bg-white border-b hover:bg-gray-50">
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap ">
                                {{ $loop->iteration }}
                            </th>
                            
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                {{ $listitem->duedate }}
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
                        </tr>   
                    @endforeach
                </table>
            </div>
            {{-- End Accordion content --}}
        </div>    
    </div>
@endforeach
<div>
    {{ $plands -> links() }}
</div>
</div>