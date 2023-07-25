<script lang="ts" setup>
defineProps({
	label: {
		type: String,
		default: 'Salva',
	},
	form: String,
	color: {
		type: String,
		default: 'blue',
	},
	text: {
		type: String,
		default: 'white',
	},
});
</script>

<template>
	<button
		type="submit"
		:form="form"
		:class="`bg-${color}-700 hover:bg-${color}-800 text-${text}`"
		class="inline-flex grow md:grow-0 justify-center rounded-md px-3 py-2 text-sm font-semibold shadow-sm sm:mt-0 sm:w-auto"
	>
		<slot></slot>
	</button>
</template>

<style scoped></style>
