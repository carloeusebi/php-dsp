<script lang="ts" setup>
import { ref } from 'vue';

import AppModal from '../AppModal.vue';
import AppButton from '../AppButton.vue';
import AppButtonBlank from '../AppButtonBlank.vue';
import AppInputElement from '../AppInputElement.vue';

import { QuestionVariableCutoff } from '@/assets/data/interfaces';
import { useGenerateId } from '@/composables';

interface Props {
	variableCutoffs: QuestionVariableCutoff[];
	modelValue?: boolean;
}

interface DeleteModal {
	show: boolean;
	cutoff?: QuestionVariableCutoff;
}

const props = withDefaults(defineProps<Props>(), {
	modelValue: false,
});
const emit = defineEmits(['save', 'update:modelValue']);

// REFS

const sexScores = ref(props.modelValue);
const showModal = ref(false);
const deleteModal = ref<DeleteModal>({ show: false });
const cutoffs = ref<QuestionVariableCutoff[]>([...props.variableCutoffs]);
const cutoffNameInputs = ref<InstanceType<typeof AppInputElement>[] | null>();

/**
 * Adds a new cutoff to the list, then focuses it.
 */
const addCutoff = () => {
	const id = useGenerateId(cutoffs.value);
	cutoffs.value.push({ id, name: '', type: 'greater-than', from: 0, to: 0 });
	// Waits for the new HTMLInputElement to be crated, and then focuses on it
	setTimeout(() => {
		cutoffNameInputs.value?.at(-1)?.inputElement?.focus();
	}, 50);
};

/**
 * Removes the cutoff withe given ID from the cutoffs array.
 * @param toDeleteId The ID of the cutoff to delete.
 */
const deleteCutoff = (toDeleteId: number | undefined) => {
	if (toDeleteId) {
		cutoffs.value = cutoffs.value.filter(({ id }) => id !== toDeleteId);
	}
	deleteModal.value.show = false;
};

/**
 * Emits `save` and closes the modal.
 */
const handleSave = () => {
	emit('save', cutoffs.value);
	showModal.value = false;
};
</script>

<template>
	<button
		type="button"
		@click="showModal = true"
		class="inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto me-4"
	>
		<span class="hidden lg:inline">Gestisci i&ThinSpace;</span>Cutoffs
	</button>

	<AppModal
		:open="showModal"
		@close="showModal = false"
	>
		<template #content>
			<div class="flex justify-between items-center">
				<h2>Gestisci i Cutoffs</h2>
				<div>
					<label class="container shrink">
						<input
							id="sex"
							type="checkbox"
							class="me-2 cursor-pointer"
							v-model="sexScores"
							@change="emit('update:modelValue', sexScores)"
						/>
						<span class="checkmark"></span>
					</label>
					<label
						for="sex"
						class="ms-7 cursor-pointer"
						>Punteggi diversi per sesso</label
					>
				</div>
			</div>
			<hr class="mb-5" />
			<form
				id="cutoffs-form"
				@submit.prevent="handleSave"
			>
				<ul>
					<li
						v-for="cutoff in cutoffs"
						:key="cutoff.id"
					>
						<div class="flex gap-10 mb-1 justify-between items-center">
							<!-- NAME -->
							<AppInputElement
								ref="cutoffNameInputs"
								v-model="cutoff.name"
								label="Nome"
								class="grow"
								:required="true"
							/>
							<!-- DELETE BUTTON -->
							<!-- @click="deleteCutoff(cutoff.id, cutoff.name)" -->
							<font-awesome-icon
								@click="
									deleteModal.show = true;
									deleteModal.cutoff = cutoff;
								"
								:icon="['fas', 'trash-can']"
								class="text-red-700 hover:text-red-800 cursor-pointer"
							/>
						</div>
						<!-- RADIOS -->
						<div class="flex gap-10 items-center my-1">
							<label class="cursor-pointer">
								<input
									class="cursor-pointer"
									type="radio"
									:name="`cutoff-${cutoff.id}-type`"
									v-model="cutoff.type"
									value="greater-than"
								/>
								Maggiore di
							</label>
							<label class="cursor-pointer">
								<input
									class="cursor-pointer"
									type="radio"
									:name="`cutoff-${cutoff.id}-type`"
									v-model="cutoff.type"
									value="lesser-than"
								/>
								Minore di
							</label>
							<label class="cursor-pointer">
								<input
									class="cursor-pointer"
									type="radio"
									:name="`cutoff-${cutoff.id}-type`"
									v-model="cutoff.type"
									value="range"
								/>
								Range
							</label>
						</div>
						<!-- RANGE CUTOFF -->
						<div v-if="cutoff.type === 'range'">
							<div class="flex gap-3 items-center">
								<span>Punteggio da</span>
								<!-- FROM -->
								<input
									type="number"
									v-model="cutoff.from"
									required
								/>
								<span>a</span>
								<input
									type="number"
									v-model="cutoff.to"
									required
								/>
								<div v-if="sexScores">Per Uomo</div>
							</div>
							<!-- only if scores per sex is enabled -->
							<div
								v-if="sexScores"
								class="flex gap-3 items-center my-2"
							>
								<span>Punteggio da</span>
								<input
									type="number"
									v-model="cutoff.femFrom"
									required
								/>
								<span>a</span>
								<input
									type="number"
									v-model="cutoff.femTo"
									required
								/>
								<div>Per Donna</div>
							</div>
							<!-- TO -->
						</div>
						<!-- GREATER THAN CUTOFF -->
						<div v-else>
							<div class="flex gap-3 items-center">
								<span> {{ cutoff.type === 'greater-than' ? 'Maggiore di' : 'Minore di' }}</span>
								<input
									type="number"
									v-model="cutoff.from"
									required
								/>
								<div v-if="sexScores">Per Uomo</div>
							</div>
							<!-- only if scores per sex is enabled -->
							<div
								v-if="sexScores"
								class="flex gap-3 items-center my-2"
							>
								<span> {{ cutoff.type === 'greater-than' ? 'Maggiore di' : 'Minore di' }}</span>
								<input
									type="number"
									v-model="cutoff.femFrom"
									required
								/>
								<div>Per Donna</div>
							</div>
						</div>
						<hr class="my-5" />
					</li>
				</ul>
			</form>

			<AppButtonBlank
				type="button"
				@click="addCutoff"
				icon="plus"
				label="Aggiungi Cutoff"
			/>

			<!-- DELETE MODAL -->
			<AppModal
				:open="deleteModal.show"
				@close="deleteModal.show = false"
				dimensions="max-w-md"
			>
				<template #content>
					<h2>Elimina</h2>
					<hr class="my-2" />
					Sicuro di voler eliminare {{ deleteModal.cutoff?.name || 'questo cutoff' }}?
				</template>
				<template #button>
					<AppButton
						color="red"
						type="button"
						@click="deleteCutoff(deleteModal.cutoff?.id)"
					>
						Elimina
					</AppButton>
				</template>
			</AppModal>
		</template>
		<template #button>
			<AppButton form="cutoffs-form"> Salva </AppButton>
		</template>
	</AppModal>
</template>

<style lang="scss" scoped>
@use '@/assets/scss/checkbox';

input[type='number'] {
	width: 60px;
	box-shadow: inset 0 0 3px gray;
	padding: 0 5px;
	border-radius: 5px;
}

label.container {
	bottom: 10px;
}
</style>
