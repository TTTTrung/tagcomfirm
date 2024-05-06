<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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
                    

                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">
                                        No.
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Email
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Name
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Role
                                    </th>
                            
                                    <th scope="col" class="px-6 py-3 text-right">
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                    
                                
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap ">
                                        {{ $loop->iteration }}
                                    </th>
                                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                        {{ $user->email}}
                                    </th>
                                    <td class="px-6 py-4">
                                        {{ $user->name }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @foreach($user->getRoleNames() as $role)
                                        {{ $role }}
                                        @endforeach
                                    </td>
                
                                    <td class="px-6 py-4 text-right">
                                        <button wire:click="openPasswordModal({{ $user->id }})"  class="font-medium text-white rounded-lg bg-yellow-400 hover:bg-yellow-500 focus:ring-4 focus:ring-red-300 px-2 py-0.5">Password</button>
                                        <button wire:click="openEditModal({{ $user->id }})"  class="font-medium text-white rounded-lg bg-yellow-400 hover:bg-yellow-500 focus:ring-4 focus:ring-red-300 px-2 py-0.5">Edit</button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Create modal --}}
    @if($showCreateModal)
    <div class="fixed inset-0 bg-gray-300 opacity-40"  wire:click="hideCreateModal"></div>
    <form class="flex flex-col justify-between bg-white rounded m-auto fixed inset-0" :style="{ 'max-height': '550px', 'max-width' : '35rem' }" wire:submit.prevent="createUser">
        <div class="bg-blue-700 text-white w-full px-4 py-3 flex items-center justify-between border-b border-gray-300">
            <div class="text-xl font-bold">Create User</div>
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
                    <label for="email" class="block mb-2 text-sm font-medium text-gray-900 ">Email</label>
                    <input type="email" wire:model="email" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required />
                    @error('email') 
                        <span class="text-red-500 text-xs">{{ $message }}</span> 
                    @enderror
                </div>
                    <div class="mb-5  mx-10">
                    <label for="name" class="block mb-2 text-sm font-medium text-gray-900 ">Name</label>
                    <input wire:model="name" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required />
                    @error('name') 
                        <span class="text-red-500 text-xs">{{ $message }}</span> 
                    @enderror
                    </div>
                    <div class="mb-5  mx-10">
                    <label for="password" class="block mb-2 text-sm font-medium text-gray-900 ">Password</label>
                    <input type="password" wire:model="password" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required />
                    @error('password') 
                        <span class="text-red-500 text-xs">{{ $message }}</span> 
                    @enderror
                    </div> 
                    <div class="mb-5 mx-10">
                        <label for="roles" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Select user role</label>
                        <select id="roles" wire:model="selectedRoles" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" >
                        {{-- @foreach ($roles as $role )
                            <option value="{{ $role->name }}">{{ $role -> name }}</option>
                        @endforeach --}}
                        <option value="">User role</option>
                        <option value="scanner">Scanner</option>
                        <option value="plAdmin">PlAdmin</option>
                        <option value="plSuperAdmin">plSuperAdmin</option>
                        </select>
                        @error('selectedRoles') 
                        <span class="text-red-500 text-xs">{{ $message }}</span> 
                    @enderror
                     </div>  
                   
                </div>
        </div>
    <div class="bg-gray-100 w-full flex justify-end p-4">
        <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5">Create user</button>
    </div>
    </form>
</div>
@endif 


@if($showEditModal)
    <div class="fixed inset-0 bg-gray-300 opacity-40"  wire:click="hideEditModal"></div>
    <form class="flex flex-col justify-between bg-white rounded m-auto fixed inset-0" :style="{ 'max-height': '550px', 'max-width' : '35rem' }" wire:submit.prevent="editUser">
        <div class="bg-yellow-400 text-white w-full px-4 py-3 flex items-center justify-between border-b border-gray-300">
            <div class="text-xl font-bold">Edit User</div>
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
                    <label for="edEmail" class="block mb-2 text-sm font-medium text-gray-900">Email</label>
                    <input type="edEmail" wire:model="edEmail" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required />
                    @error('edEmail') 
                        <span class="text-red-500 text-xs">{{ $message }}</span> 
                    @enderror
                </div>
                    <div class="mb-5  mx-10">
                    <label for="edName" class="block mb-2 text-sm font-medium text-gray-900 ">Name</label>
                    <input id="edName" wire:model="edName" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 " required />
                    @error('edName') 
                        <span class="text-red-500 text-xs">{{ $message }}</span> 
                    @enderror
                    </div>
                    <div class="mb-5 mx-10">
                        <label for="edSelectedRoles" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Change user role</label>
                        <select wire:model="edSelectedRoles" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" >
                        {{-- @foreach ($roles as $role )
                            <option value="{{ $role->name }}">{{ $role -> name }}</option>
                        @endforeach --}}
                        <option value="">User role</option>
                        <option value="scanner">Scanner</option>
                        <option value="plAdmin">PlAdmin</option>
                        <option value="plSuperAdmin">plSuperAdmin</option>
                        </select>
                        @error('edSelectedRoles') 
                        <span class="text-red-500 text-xs">{{ $message }}</span> 
                    @enderror
                     </div>  
                </div>
        </div>
    <div class="bg-gray-100 w-full flex justify-end p-4">
        <button type="submit" class="text-white bg-yellow-400 hover:bg-yellow-500 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5">Apply</button>
    </div>
    </form>
</div>
@endif

@if($showPasswordModal)
    <div class="fixed inset-0 bg-gray-300 opacity-40"  wire:click="hidePasswordModal"></div>
    <form class="flex flex-col justify-between bg-white rounded m-auto fixed inset-0" :style="{ 'max-height': '550px', 'max-width' : '35rem' }" wire:submit.prevent="changePassword">
        <div class="bg-yellow-400 text-white w-full px-4 py-3 flex items-center justify-between border-b border-gray-300">
            <div class="text-xl font-bold">Change user Password</div>
            <button wire:click="hidePasswordModal" class="focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
   
        <div class="flex-grow bg-white w-full flex flex-col items-center justify-start overflow-y-auto">
            <!-- Form Content -->
            <div class="w-full">
                <div class="mb-5 mt-5 mx-10">
                    <label for="new_passowrd" class="block mb-2 text-sm font-medium text-gray-900">New Password</label>
                    <input type="password" wire:model="newPassword" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required />
                    @error('newPassword') 
                        <span class="text-red-500 text-xs">{{ $message }}</span> 
                    @enderror
                </div>
                    <div class="mb-5  mx-10">
                    <label for="conPassword" class="block mb-2 text-sm font-medium text-gray-900 ">Confirm Password</label>
                    <input type="password"  wire:model="conPassword" class="shadow-sm bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 " required />
                    @error('conPassword') 
                        <span class="text-red-500 text-xs">{{ $message }}</span> 
                    @enderror
                    </div>               
                </div>
        </div>
    <div class="bg-gray-100 w-full flex justify-end p-4">
        <button type="submit" class="text-white bg-yellow-400 hover:bg-yellow-500 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5">Save</button>
    </div>
    </form>
</div>
@endif
</div>

