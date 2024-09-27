<div>
    <div class="py-12">
        <div class="max-w-8xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-end">
                <button type="button"
                wire:click = "openExport"  
                class="text-white bg-teal-400 hover:bg-teal-500 focus:ring-4 focus:ring-teal-600 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-teal-400 dark:hover:bg-teal-500 focus:outline-none dark:focus:ring-teal-600">
                Export History</button>
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
                                    Customer ID. 
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Plan Id.
                                </th>
                                 <th scope="col" class="px-6 py-3">
                                    Issue 
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
                                    Status
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Scan by
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Approve by
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Description
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($historys as $history)
                            <tr class="bg-white border-b hover:bg-gray-50">
                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap ">
                                    {{ $loop->iteration }}
                                </th>
                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                    {{ $history->customer}}
                                </th>
                                
                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                    {{ $history->planid }}
                                </th>
                                <td class="px-6 py-4">
                                    {{ $history->issue}}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $history->outside }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $history->thpart }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $history->qty }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $history->status}}
                                </td>
                                 <td class="px-6 py-4">
                                    {{ optional($history->createdBy)->name}}
                                </td>   
                                 <td class="px-6 py-4">
                                    {{ optional($history->updatedBy)->name }}
                                </td>   
                                <td class="px-6 py-4">
                                    {{ $history->description}}
                                </td>
                            </tr>   
                            @endforeach 
                        </tbody>
                    </table>
                    </div> 
                      <div class="py-2 px-2">
                            {{ $historys->onEachSide(3)->links() }}
                       </div>  
                </div>
            </div>
        </div>
    </div> 
       @if($showExportHistory)
        <div class="fixed inset-0 bg-gray-300 opacity-40"  wire:click="closeExport"></div>
        <form wire:submit.prevent="exportHistory" class="flex flex-col justify-between bg-white rounded m-auto fixed inset-0" 
        :style="{ 'max-height': '300px', 'max-width' : '500px' }">
            <div class="bg-teal-400 text-white w-full px-4 py-3 flex items-center justify-between border-b border-gray-300">
                <div class="text-xl font-bold">Export History</div>
                <button wire:click="closeExport" type="button" class="focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="flex-grow bg-white w-full flex flex-col items-center justify-start overflow-y-auto">
                <div class="flex justify-start w-full">
                   <div class="w-full ml-5 m-3">
                        <label for="FromDate" class="text-xs block uppercase tracking-wide text-gray-700 font-bold">From Date</label>
                        <input id="FromDate" wire:model="fromDate" type="date" class="w-full p-2 border border-gray-300 text-sm rounded" /> 
                        @error('exFromDate')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="w-full mr-5 m-3">
                        <label for="toDate" class="text-xs block uppercase tracking-wide text-gray-700 font-bold">To Date</label>
                        <input id="toDate" wire:model="toDate" type="date" class="w-full p-2 border border-gray-300 text-sm rounded" /> 
                        @error('exToDate')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="bg-gray-100 w-full flex justify-between p-4">
                <div class="flex">
                    
                </div>
                <div>
                 <button type="submit" 
        class="text-white bg-teal-400 hover:bg-teal-400 focus:ring-4 focus:outline-none focus:ring-teal-300 font-medium rounded-lg text-sm px-5 py-2.5">Export</button>
                </div>
            </div>
        </form>
        </div>
    @endif  
</div>
