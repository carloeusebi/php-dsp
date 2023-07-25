<script lang="ts" setup>
import {
	Dialog,
	DialogPanel,
	TransitionChild,
	TransitionRoot,
} from '@headlessui/vue';
import { useRoute } from 'vue-router';
import router from '@/routes';
import { watch } from 'vue';

const route = useRoute();

const props = defineProps({
	open: Boolean,
	dimensions: {
		type: String,
		default: 'sm:max-w-4xl',
	},
});

// Watcher add a route hash, when back button si pressed the back button removes the hash and closes the modal. If it were

watch(
	() => route.hash,
	() => {
		if (route.hash === '') {
			emit('close');
		}
	}
);
watch(
	() => props.open,
	() => {
		if (props.open === true) {
			router.push({ hash: '#' });
		}
	}
);

const emit = defineEmits(['close']);
</script>

<template>
	<TransitionRoot
		as="template"
		:show="open"
	>
		<Dialog
			as="div"
			class="relative z-30"
			@close="emit('close')"
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
				<div
					class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
				/>
			</TransitionChild>

			<div
				class="fixed inset-0 z-10 overflow-y-auto"
				ref="modal"
			>
				<div
					class="flex min-h-full items-center justify-center text-center sm:items-center"
				>
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
									@click="emit('close')"
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
