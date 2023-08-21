<script lang="ts" setup>
import { ref } from 'vue';
import AppModal from '../AppModal.vue';
import AppButton from '../AppButton.vue';
import { QuestionItemI } from '@/assets/data/interfaces';

interface Props {
	variableItems: number[];
	items: QuestionItemI[];
}

const props = defineProps<Props>();
const emit = defineEmits(['save']);

const showModal = ref(false);
const selectedItems = ref<number[]>(props.variableItems);

/**
 * Selects all the items.
 */
const selectAllItems = () => {
	//@ts-ignore
	selectedItems.value = props.items.map(item => item.id);
};

/**
 * Unselects all the items.
 */
const deselectAllItems = () => {
	selectedItems.value = [];
};

/**
 * Updates the variable' selected items.
 */
const handleSaveBtnClick = () => {
	showModal.value = false;
	emit('save', selectedItems.value);
};
</script>

<template>
	<button
		type="button"
		class="inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto me-4"
		@click="showModal = true"
	>
		<span class="hidden lg:inline">Scegli le &ThinSpace;</span>Domande
	</button>

	<!-- ITEMS MODAL -->
	<AppModal
		:open="showModal"
		@close="showModal = false"
	>
		<!-- content -->
		<template #content>
			<h2>Seleziona le domande</h2>
			<hr />
			<div v-if="!variableItems">## Errore</div>
			<div
				v-else
				class="flex justify-between gap-6"
			>
				<div class="flex gap-2 my-2">
					<div
						class="cursor-pointer p-1 text-blue-500 hover:text-blue-700"
						@click="selectAllItems"
					>
						Seleziona Tutte
					</div>
					<div
						class="cursor-pointer p-1 text-blue-500 hover:text-blue-700"
						@click="deselectAllItems"
					>
						Deseleziona Tutte
					</div>
				</div>
			</div>
			<ul class="text-sm max-h-[300px] overflow-y-auto overflow-x-hidden mb-5 shadow-inner p-4 rounded-lg">
				<li
					v-for="(item, i) in items"
					:key="item.id"
				>
					<label class="container shrink">
						<input
							:id="`item:${item.id}`"
							type="checkbox"
							class="cursor-pointer"
							:value="item.id"
							v-model="selectedItems"
						/>
						<span class="checkmark"></span>
					</label>
					<label
						:for="`item:${item.id}`"
						class="truncate ms-7 cursor-pointer inline-block"
						>{{ i + 1 }}. - {{ item.text }}</label
					>
				</li>
			</ul>
		</template>
		<template #button>
			<AppButton
				type="button"
				@click="handleSaveBtnClick"
			>
				Salva
			</AppButton>
		</template>
	</AppModal>
</template>

<style lang="scss" scoped>
@use '@/assets/scss/checkbox';

label.container {
	bottom: 14px;
}
</style>
