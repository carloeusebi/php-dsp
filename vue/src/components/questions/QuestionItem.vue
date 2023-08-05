<script lang="ts" setup>
import AppInputElement from '../AppInputElement.vue';
import { QuestionItemI } from '@/assets/data/interfaces';

interface Props {
	item: QuestionItemI;
}

defineProps<Props>();
const emit = defineEmits(['delete-item']);
</script>

<template>
	<div class="flex items-end">
		<div class="grow">
			<!-- CHECKBOX -->
			<label class="container shrink">
				<input
					v-model="item.reversed"
					type="checkbox"
					class="me-2 cursor-pointer"
				/>
				<span class="checkmark"></span>
			</label>
			<!-- ITEM -->
			<AppInputElement
				class="grow ms-8"
				v-model="item.text"
				:id="`answer-${item.id}`"
			/>
		</div>
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
