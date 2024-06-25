<div>
    <div class="py-12">
        <div class="max-w-8xl mx-auto sm:px-6 lg:px-8">
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
            <div class="flex items-center justify-between p-4">
            <div class="relative mt-1 mb-2">
                <div class="absolute inset-y-0 rtl:inset-r-0 start-0 flex items-center ps-3 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                    </svg>
                </div>
                <input type="text" wire:model.live.debounce.300ms="search" class="block pt-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Search for items">
            </div>
            <div class="flex ml-2">
                <div class="flex space-x-1 items-center">
                    <label class="w-20 text-sm font-medium text-gray-900"></label>
                    <input id="due" wire:model.live="timedue" type="date" class="w-full p-2 border border-gray-300 text-sm rounded" required /> 
                </div>
                <div class="flex space-x-1 items-center">
                    <label class="w-20 text-sm font-medium text-gray-900"></label>
                    <select wire:model.live="company"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 ">
                        <option value="">All</option>
                        @foreach ($get_com as $company)
                        <option value="{{ $company }}">{{ $company }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex space-x-1 items-center">
                    <label class="w-20 text-sm font-medium text-gray-900"></label>
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
                <div x-data="{ isOpen: false }" @click="isOpen = !isOpen" class="tab px-5 py-2 border-2 @if ($pland->status == 'done') border-green-600 @endif  
                    bg-slate-100 shadow-lg relative mb-4 rounded-md cursor-pointer">
                    <div 
                        class="flex justify-between items-center font-semibold text-lg after:absolute after:right-5 after:text-2xl after:text-gray-400 hover:after:text-gray-950 peer-checked:after:transform peer-checked:after:rotate-45">
                        <div class="flex">
                            <h2 class="w-8 h-8 bg-sky-300 text-white flex justify-center items-center rounded-sm mr-3">{{ $index + 1 }}</h2>
                            <h3>{{ $pland->plan_id}}</h3>
                            <h3 class="ml-1">{{ $pland->company_name }}</h3>
                        </div>
                        <div class="flex">
                            @if ($pland->status == 'approved')
                            <button wire:click.stop="markDone('{{ $pland->id }}', '{{ $pland->status }}')"  class="text-white bg-red-500 hover:bg-green-700  font-medium rounded-lg text-sm px-3 py-1.5 me-2 mb-2">
                                Mark
                            </button>  
                            @endif
                            @if(auth()->id() == $pland->created_by || auth()->user()->hasRole('superAdmin'))
                            <button wire:click.stop="openMoveModal({{ $pland->id }})" class="font-medium text-white rounded-lg bg-yellow-400 hover:bg-yellow-500 focus:ring-4 focus:ring-red-300 text-sm px-3 py-1.5 me-2 mb-2">
                                Move
                            </button>
                            @endif             
                            <button wire:click.stop="export({{ $pland->id }})"  class="text-white bg-green-500 hover:bg-green-700  font-medium rounded-lg text-sm px-3 py-1.5 me-2 mb-2">
                                Export Tag
                            </button>
                            <button wire:click.stop="oneoracle({{ $pland->id }})"  class="text-white bg-cyan-400 hover:bg-cyan-500  font-medium rounded-lg text-sm px-3 py-1.5 me-2 mb-2">
                                One
                            </button>
                            <button wire:click.stop="oracle({{ $pland->id }})"  class="text-white bg-sky-500 hover:bg-sky-600  font-medium rounded-lg text-sm px-3 py-1.5 me-2 mb-2">
                                Many
                            </button> 
                            <button wire:click.stop="ship({{ $pland->id }})"  class="text-white bg-sky-500 hover:bg-sky-600  font-medium rounded-lg text-sm px-3 py-1.5 me-2 mb-2">
                                Ship
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
                                        PR.
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
                                        Ship to
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        status 
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
                                        {{ $listitem->pr }}
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
                                    @if ($listitem->flag == true)
                                    <td class="px-6 py-4 justify-center">
                                    <span class="flex w-3 h-3 me-3 bg-green-500 rounded-full"></span>
                                    </td> 
                                    @else
                                    <td class="px-6 py-4 justify-center">
                                        <div>
                                            <span class="flex w-3 h-3 me-3 bg-red-500 rounded-full"></span>
                                        </div>
                                    </td> 
                                    @endif
                                </tr>   
                            @endforeach
                        </table>
                    </div>
                    {{-- End Accordion content --}}
                </div>    
            </div>
        @endforeach
        <div class="py-2 px-2">
            {{ $plands -> links() }}
        </div>
    </div>
</div>
</div>




@if($movemodal)
<div class="fixed inset-0 p-4 flex flex-wrap justify-center items-center w-full h-full z-[1000] before:fixed before:inset-0 before:w-full before:h-full before:bg-[rgba(0,0,0,0.5)] overflow-auto font-[sans-serif]">
    <div class="fixed inset-0  bg-gray-300 opacity-40" wire:click="closeMoveModal"></div>
      <div class="w-full  max-w-md bg-white shadow-lg rounded-md p-6 relative">
        <svg wire:click="closeMoveModal" xmlns="http://www.w3.org/2000/svg"
          class="w-3.5 cursor-pointer shrink-0 fill-black hover:fill-red-500 float-right" viewBox="0 0 320.591 320.591">
          <path
            d="M30.391 318.583a30.37 30.37 0 0 1-21.56-7.288c-11.774-11.844-11.774-30.973 0-42.817L266.643 10.665c12.246-11.459 31.462-10.822 42.921 1.424 10.362 11.074 10.966 28.095 1.414 39.875L51.647 311.295a30.366 30.366 0 0 1-21.256 7.288z"
            data-original="#000000"></path>
          <path
            d="M287.9 318.583a30.37 30.37 0 0 1-21.257-8.806L8.83 51.963C-2.078 39.225-.595 20.055 12.143 9.146c11.369-9.736 28.136-9.736 39.504 0l259.331 257.813c12.243 11.462 12.876 30.679 1.414 42.922-.456.487-.927.958-1.414 1.414a30.368 30.368 0 0 1-23.078 7.288z"
            data-original="#000000"></path>
        </svg>
        <div class="my-8 text-center justify-center flex items-center">
            <svg width="196px" height="196px" viewBox="-0.5 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M10.8809 16.15C10.8809 16.0021 10.9101 15.8556 10.967 15.7191C11.024 15.5825 11.1073 15.4586 11.2124 15.3545C11.3175 15.2504 11.4422 15.1681 11.5792 15.1124C11.7163 15.0567 11.8629 15.0287 12.0109 15.03C12.2291 15.034 12.4413 15.1021 12.621 15.226C12.8006 15.3499 12.9399 15.5241 13.0211 15.7266C13.1024 15.9292 13.122 16.1512 13.0778 16.3649C13.0335 16.5786 12.9272 16.7745 12.7722 16.9282C12.6172 17.0818 12.4204 17.1863 12.2063 17.2287C11.9922 17.2711 11.7703 17.2494 11.5685 17.1663C11.3666 17.0833 11.1938 16.9426 11.0715 16.7618C10.9492 16.5811 10.8829 16.3683 10.8809 16.15ZM11.2408 13.42L11.1008 8.20001C11.0875 8.07453 11.1008 7.94766 11.1398 7.82764C11.1787 7.70761 11.2424 7.5971 11.3268 7.5033C11.4112 7.40949 11.5144 7.33449 11.6296 7.28314C11.7449 7.2318 11.8697 7.20526 11.9958 7.20526C12.122 7.20526 12.2468 7.2318 12.3621 7.28314C12.4773 7.33449 12.5805 7.40949 12.6649 7.5033C12.7493 7.5971 12.813 7.70761 12.8519 7.82764C12.8909 7.94766 12.9042 8.07453 12.8909 8.20001L12.7609 13.42C12.7609 13.6215 12.6809 13.8149 12.5383 13.9574C12.3958 14.0999 12.2024 14.18 12.0009 14.18C11.7993 14.18 11.606 14.0999 11.4635 13.9574C11.321 13.8149 11.2408 13.6215 11.2408 13.42Z" fill="#f7fb04"></path> <path d="M12 21.5C17.1086 21.5 21.25 17.3586 21.25 12.25C21.25 7.14137 17.1086 3 12 3C6.89137 3 2.75 7.14137 2.75 12.25C2.75 17.3586 6.89137 21.5 12 21.5Z" stroke="#f7fb04" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
        </div>
        <h4 class="text-xl text-center font-semibold mt-6">Are you sure you want to move plan back</h4>
        <div class="flex flex-col space-y-2">
          <button wire:click="movePlan" type="button"
            class="px-6 py-2.5 rounded-md text-white text-sm font-semibold border-none outline-none bg-yellow-400 hover:bg-yellow-500 focus:ring-4">Approve</button>
          <button wire:click="closeMoveModal" type="button"
            class="px-6 py-2.5 rounded-md text-black text-sm font-semibold border-none outline-none bg-gray-200 hover:bg-gray-300 active:bg-gray-200">Cancel</button>
        </div>
      </div>
    </div>
 @endif
</div>

