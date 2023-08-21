<script lang="ts" setup>
import { Dialog, DialogPanel, TransitionChild, TransitionRoot } from '@headlessui/vue';
import { useRoute } from 'vue-router';
import router from '@/routes';
import { computed, watch } from 'vue';

const route = useRoute();

const props = defineProps({
	open: Boolean,
	dimensions: {
		type: String,
		default: 'sm:max-w-4xl',
	},
	/**
	 * Disables history mode, should only be set to True when the modal can't open other modals from inside.
	 * History Mode is used to close the modals with the back button.
	 */
	disableHistoryMode: {
		type: Boolean,
		default: false,
	},
});

const emit = defineEmits(['close']);

/**
 * The modal ID value is calculated so that it is always higher than the previously opened modal
 */
const modal_id = computed(() => ((route.query.modal_id as string) ? parseInt(route.query.modal_id as string) + 1 : 1));
/**
 * The ID the modal had when it was opened
 */
let assignedIdWhenOpened: number;

// Watch for changes in the 'open' prop to manage modal behavior
watch(
	() => props.open,
	newValue => {
		if (props.disableHistoryMode) return;
		// If modal is being opened
		if (newValue === true) {
			router.push({ query: { modal_id: modal_id.value } });
			assignedIdWhenOpened = modal_id.value;
		}

		// If modal is being closed from the outside this will update the route history and keep it keep it coherent with the modal state
		if (newValue === false && parseInt(route.query.modal_id as string) === assignedIdWhenOpened) {
			router.back();
		}
	}
);

/**
 * Watches the modal_id param in the url query, if it changes it either means a new modal has opened, or the back button was pressed.
 * If the new value is not null and it is higher then the modal's assigned id it means a new modal has opened, in this case we do nothing.
 * If the new value is null it means that the current modal was the only opened modal when the back button was pressed, so we close the modal.
 * If the new value is not null but it lower than the assigned Id it means we have more than one open modal, and the back button was pressed. We close the modal with the highest id, which is the last opened one, the top one.
 */
watch(
	() => route.query.modal_id,
	newValue => {
		if (props.disableHistoryMode) return;
		if (!route.query.modal_id || parseInt(newValue as string) < assignedIdWhenOpened) {
			emit('close');
		}
	}
);

const closeModal = () => {
	emit('close');
};
</script>

<template>
	<TransitionRoot
		as="template"
		:show="open"
	>
		<Dialog
			as="div"
			class="relative z-30"
			@close="closeModal"
		>
			<TransitionChild
				as="template"
				enter="ease-out duration-300"
				enter-from="opacity-0"
				enter-to="opacity-100"
				leave="ease-in duration-200"
				leave-from="opacity-100"
				leave-to="opacity-0"
			>
				<div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" />
			</TransitionChild>

			<div
				class="fixed inset-0 z-10 overflow-y-auto"
				ref="modal"
			>
				<div class="flex min-h-full items-center justify-center text-center sm:items-center">
					<TransitionChild
						as="template"
						enter="ease-out duration-300"
						enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
						enter-to="opacity-100 translate-y-0 sm:scale-100"
						leave="ease-in duration-200"
						leave-from="opacity-100 translate-y-0 sm:scale-100"
						leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
					>
						<DialogPanel
							class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all w-full"
							:class="dimensions"
						>
							<div
								id="modal-container"
								class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4"
							>
								<!-- CONTENT SLOT -->

								<slot name="content"></slot>

								<!-- /SLOT -->
							</div>
							<div class="bg-gray-50 py-3 flex flex-row-reverse px-6">
								<!-- CONFIRM BUTTON SLOT -->

								<slot name="button"></slot>

								<!-- /SLOT -->
								<button
									type="button"
									class="inline-flex grow md:grow-0 justify-center items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 mt-0 sm:w-auto me-4"
									@click="closeModal"
									ref="cancelButtonRef"
								>
									Chiudi
								</button>
							</div>
						</DialogPanel>
					</TransitionChild>
				</div>
			</div>
		</Dialog>
	</TransitionRoot>
</template>
