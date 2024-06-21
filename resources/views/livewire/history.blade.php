<div>
    <div class="py-12">
        <div class="max-w-8xl mx-auto sm:px-6 lg:px-8">
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
                                    {{ $history->planid }}</th>
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
                        <div class="py-2 px-2">
                            {{ $historys->onEachSide(3)->links() }}
                        </div>                         
                    </div> 
                </div>
            </div>
        </div>
    </div> 
</div>
