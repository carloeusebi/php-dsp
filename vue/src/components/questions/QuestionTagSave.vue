<script lang="ts" setup>
import { Ref, computed, ref } from 'vue';

import AppModal from '../AppModal.vue';
import AppAlert from '../AppAlert.vue';
import AppButton from '../AppButton.vue';
import AppInputElement from '../AppInputElement.vue';

import { Errors, NewTag, Tag } from '@/assets/data/interfaces';
import { useSaveToStore } from '@/composables';
import { useTagsStore } from '@/stores';

interface Props {
	open: boolean;
	type?: 'create' | 'update';
	tag?: Tag | NewTag;
}

const props = withDefaults(defineProps<Props>(), {
	tag: () => ({ tag: '', color: '#000000' }),
	type: () => 'create',
});
const emit = defineEmits(['close']);

const title = props.type === 'create' ? 'Crea un nuovo tag' : `Modifica ${props.tag.tag}`;

const errors: Ref<Errors> = ref({});
const errorsStr = computed(() => {
	const keys = Object.keys(errors.value);
	return keys.reduce((str, key) => (str += `${errors.value[key]}<br>`), '');
});

const saveTag = async () => {
	errors.value = {};
	errors.value = await useSaveToStore(props.tag, useTagsStore());
	if (!errorsStr.value) emit('close');
};
</script>

<template>
	<!-- MODAL -->
	<AppModal
		:open="open"
		@close="emit('close')"
		dimensions="max-w-xs"
	>
		<template #content>
			<h2>{{ title }}</h2>
			<hr class="my-5" />

			<!-- ALERT -->
			<AppAlert
				:show="errorsStr.length > 0"
				type="warning"
				title="Attenzione"
				:message="errorsStr"
				class="my-4"
			/>

			<!-- FORM -->
			<form
				id="new-tag-from"
				class="flex items-end gap-6"
				@submit.prevent="saveTag"
			>
				<AppInputElement
					label="Nome del tag"
					v-model="tag.tag"
					:required="true"
				/>
				<input
					type="color"
					v-model="tag.color"
					required
				/>
			</form>
		</template>
		<template #button>
			<AppButton form="new-tag-from"> Salva </AppButton>
		</template>
	</AppModal>
</template>

<style scoped></style>
