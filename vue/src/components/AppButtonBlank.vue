<script lang="ts" setup>
defineProps({
	color: {
		type: String,
		default: 'blue',
	},
	label: String,
	icon: {
		type: String,
	},
});
</script>

<template>
	<button
		type="button"
		class="flex group items-stretch text-white shadow-border text-sm font-sans font-bold rounded overflow-hidden"
	>
		<div
			v-if="icon"
			class="flex items-center py-3 md:py-1 lg:py-2 px-3"
			:class="`bg-${color}-500 group-hover:bg-${color}-600`"
		>
			<font-awesome-icon :icon="['fas', icon]" />
		</div>
		<div
			class="flex items-center py-3 md:py-1 lg:py-2 px-4 hidden md:flex"
			:class="`bg-${color}-600 group-hover:bg-${color}-700`"
		>
			{{ label }}
		</div>
	</button>
</template>

<style scoped></style>
