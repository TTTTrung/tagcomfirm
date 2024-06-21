<div>
    <div class="py-12">
        <div class="max-w-md mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form class="max-w-sm mx-auto" wire:submit.prevent="unlock">
                        <div class="mb-5">
                        <label class="block mb-2 text-sm font-medium text-gray-900 ">User name</label>
                        <input wire:model="email" autocomplete="off" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 " required />
                        </div>
                        <div class="mb-5">
                        <label class="block mb-2 text-sm font-medium text-gray-900 ">Password</label>
                        <input wire:model="password" type="password" autocomplete="off" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 " required /> 
                        </div>
                        <div class="mb-5">
                      
                       <div class="mb-5">
                        <label class="block mb-2 text-sm font-medium text-gray-900 ">Description</label>
                        <input   wire:model='description' autocomplete="off" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 " required />
                    
                        </div>
                        <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">submit</button> </form>
                </div>
            </div>
        </div>
    </div>
</div>
