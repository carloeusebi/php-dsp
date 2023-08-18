<script lang="ts" setup>
//@ts-ignore
import { clickOutSide as vClickOutSide } from '@mahdikhashan/vue3-click-outside';
import { ChevronDownIcon } from '@heroicons/vue/20/solid';
import { ref } from 'vue';
import { useRoute } from 'vue-router';

interface Props {
	items: Array<any>;
}
defineProps<Props>();

const showDropdown = ref(false);
const dropdownButton = ref<HTMLButtonElement>();
const route = useRoute();

/**
 * Closes the dropdown ONLY if there are no open modals
 */
const handleClickOutside = (event: Event) => {
	if (route.query.modal_id) return;
	const clickedElement = event.target as HTMLElement;
	if (dropdownButton.value?.contains(clickedElement)) return;
	showDropdown.value = false;
};
</script>

<template>
	<div
		as="div"
		class="relative inline-block text-left"
	>
		<div>
			<button
				type="button"
				@click="showDropdown = !showDropdown"
				ref="dropdownButton"
				class="inline-flex w-full justify-center gap-x-1.5 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm border border-gray-300 ring-inset ring-gray-300 hover:bg-gray-50"
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
			v-click-out-side="handleClickOutside"
			class="absolute right-0 z-10 mt-2 w-68 px-4 py-2 origin-top-right rounded-md bg-white shadow-lg border-gray-300 ring-black ring-opacity-5 focus:outline-none"
		>
			<slot name="items"></slot>
		</div>
	</div>
</template>
