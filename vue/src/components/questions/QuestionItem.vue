<script lang="ts" setup>
import AppInputElement from '../AppInputElement.vue';
import { QuestionItemI } from '@/assets/data/interfaces';

interface Props {
	item: QuestionItemI;
	index: number;
}

defineProps<Props>();
const emit = defineEmits(['delete-item']);
</script>

<template>
	<!-- CHECKBOX -->
	<label class="container">
		<input
			v-model="item.reversed"
			type="checkbox"
			class="me-2 cursor-pointer"
		/>
		<span class="checkmark"></span>
	</label>
	<!-- ITEM -->
	<div class="flex items-end">
		<span class="ms-7 me-2 self-center min-w-[25px] text-center cursor-move select-none">{{ index + 1 }}.</span>
		<AppInputElement
			class="grow"
			v-model="item.text"
			:id="`answer-${item.id}`"
		/>
		<font-awesome-icon
			@click="emit('delete-item', item.id)"
			class="ms-3 cursor-pointer text-red-700 hover:text-red-800 mb-2 md:mb-0"
			:icon="['fas', 'trash-can']"
		/>
	</div>
</template>

<style lang="scss" scoped>
@use '@/assets/scss/checkbox';
</style>
