<script setup lang="ts"></script>

<template>
  <div class="flex-1 px-2 flex justify-center lg:ml-6 lg:justify-end">
    <div class="max-w-lg w-full lg:max-w-xs">
      <label
        for="search"
        class="sr-only"
      >Search</label>
      <div class="relative text-gray-400 focus-within:text-gray-600">
        <div class="pointer-events-none absolute inset-y-0 left-0 pl-3 flex items-center" />
        <input 
          id="search" 
          class="
                        block w-full bg-white py-2 pl-10 pr-3 border border-transparent rounded-md leading-5 text-gray-900 placeholder-gray-500 
                        focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-indigo-600 focus:ring-white focus:border-white 
                        sm:text-sm
                    "
          value="test" 
          placeholder="Search" 
          type="search" 
          name="search"
        >
      </div>
    </div>
  </div>
</template>