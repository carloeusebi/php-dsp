<script lang="ts" setup>
import { ref } from 'vue';
import { ChevronDownIcon } from '@heroicons/vue/20/solid';

interface Props {
	items: Array<any>;
}
defineProps<Props>();

const showDropdown = ref(false);
</script>

<template>
	<div
		as="div"
		class="relative inline-block text-left"
	>
		<div>
			<button
				@click="showDropdown = !showDropdown"
				class="inline-flex w-full justify-center gap-x-1.5 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
			>
				<slot name="button"></slot>
				<ChevronDownIcon
					class="-mr-1 h-5 w-5 text-gray-400"
					aria-hidden="true"
				/>
			</button>
		</div>

		<div
			v-if="showDropdown"
			class="absolute right-0 z-10 mt-2 w-56 px-4 py-2 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
		>
			<slot name="items"></slot>
		</div>
	</div>
</template>
