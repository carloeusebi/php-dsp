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
		class="flex group items-center text-white shadow-border text-sm font-sans font-bold rounded overflow-hidden"
	>
		<div
			v-if="icon"
			:class="`bg-${color}-500 group-hover:bg-${color}-600`"
			class="py-3 md:py-1 lg:py-2 px-3 h-full"
		>
			<font-awesome-icon :icon="['fas', icon]" />
		</div>
		<div
			class="py-3 md:py-1 lg:py-2 px-4 hidden md:inline"
			:class="`bg-${color}-600 group-hover:bg-${color}-700`"
		>
			{{ label }}
		</div>
	</button>
</template>

<style scoped></style>
