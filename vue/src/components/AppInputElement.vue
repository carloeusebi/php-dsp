<script lang="ts" setup>
import { ref } from 'vue';

type Type = 'text' | 'date' | 'email' | 'file' | 'tel' | 'select' | 'textarea' | 'number';

interface Props {
	modelValue?: string | number;
	label?: string;
	required?: boolean;
	id?: string;
	type?: Type;
}

withDefaults(defineProps<Props>(), {
	required: false,
	type: 'text',
});

const emit = defineEmits(['update:modelValue', 'custom-change']);

const inputElement = ref<HTMLInputElement | null>(null);

defineExpose({ inputElement });

const inputClass =
	'block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer';

const labelClass =
	'peer-focus:font-medium absolute text-sm text-gray-500 duration-300 transform -translate-y-6 scale-75 top-3 z-5 origin-[0] peer-focus:left-0 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6';
</script>

<template>
	<div class="relative">
		<!-- SELECT -->
		<select
			v-if="type === 'select'"
			:id="id || label"
			:value="modelValue"
			@change="emit('update:modelValue', ($event.target as HTMLSelectElement).value)"
			:required="required"
			:class="inputClass"
		>
			<slot></slot>
		</select>
		<!-- TEXTAREA -->
		<textarea
			v-else-if="type === 'textarea'"
			:id="id || label"
			:value="modelValue"
			@input="emit('update:modelValue', ($event.target as HTMLTextAreaElement).value)"
			rows="5"
			:class="inputClass"
		>
		</textarea>
		<!-- INPUT -->
		<input
			v-else
			ref="inputElement"
			:value="modelValue"
			@input="emit('update:modelValue', ($event.target as HTMLInputElement).value)"
			@change="emit('custom-change', $event)"
			:type="type"
			:id="id || label"
			:class="inputClass"
			placeholder=" "
			:required="required"
		/>
		<label
			:for="id || label"
			:class="labelClass"
			>{{ label }}</label
		>
	</div>
</template>

<style scoped>
/* to hide Google autofill */
input:-webkit-autofill,
input:-webkit-autofill:hover,
input:-webkit-autofill:focus,
textarea:-webkit-autofill,
textarea:-webkit-autofill:hover,
textarea:-webkit-autofill:focus,
select:-webkit-autofill,
select:-webkit-autofill:hover,
select:-webkit-autofill:focus {
	-webkit-box-shadow: 0 0 0px 1000px #fff inset;
}

textarea {
	resize: none;
}
</style>
