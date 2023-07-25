<script lang="ts" setup>
import { ref } from 'vue';

const searchWord = ref('');

const emit = defineEmits(['key-press']);
</script>

<template>
	<div class="flex absolute inset-y-0 left-0 items-center pl-3 pointer-events-none">
		<svg
			class="w-5 h-5 text-gray-500"
			fill="currentColor"
			viewBox="0 0 20 20"
			xmlns="http://www.w3.org/2000/svg">
			<path
				fill-rule="evenodd"
				d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
				clip-rule="evenodd"></path>
		</svg>
	</div>
	<input
		v-model.trim="searchWord"
		@keyup="emit('key-press', searchWord)"
		type="search"
		id="search"
		class="min-w-0 bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 grow shrink block pl-10 p-2.5"
		placeholder="Cerca" />
</template>
