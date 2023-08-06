<script lang="ts" setup>
defineProps({
	color: {
		type: String,
		default: 'blue',
	},
	label: {
		type: String,
		default: 'Aggiungi',
	},
	icon: String,
});
</script>

<template>
	<button
		type="button"
		:class="`text-${color}-700 hover:text-${color}-800 focus:text-${color}-900 outline-${color}-900`"
		class="font-medium rounded-md px-5 py-2.5 outline-0 lg:focus:outline-2"
	>
		<slot></slot>
	</button>
</template>

<style scoped></style>
