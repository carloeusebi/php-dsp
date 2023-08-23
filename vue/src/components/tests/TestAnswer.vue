<script lang="ts" setup>
defineProps({
	index: Number,
	answer: String,
});
</script>

<template>
	<div class="score flex-shrink-0">{{ index }}</div>
	<div class="px-2 flex-grow">
		{{ answer }}
	</div>
	<div class="absolute flex items-center bg-[#ecf5ef] top-1 bottom-1 right-1">
		<font-awesome-icon :icon="['fas', 'check']" />
	</div>
</template>

<style lang="scss" scoped>
$primary-color: #6ecc84;
$secondary-color: #254d32;
li {
	position: relative;
	display: flex;
	align-items: center;
	min-height: 40px;
	width: 100%;

	margin-bottom: 8px;
	padding: 4px 0;
	border-radius: 4px;
	background-color: #{$primary-color}19;
	color: $secondary-color;
	box-shadow: $secondary-color 0 0 0 1px inset;

	cursor: pointer;
}

.score {
	width: 24px;
	height: 24px;
	margin: 4px 8px;

	display: flex;
	align-items: center;
	justify-content: center;

	border-radius: 2px;
	border: 1px solid $secondary-color;
	background-color: rgba(255, 255, 255, 0.8);
	color: $secondary-color;

	font-family: sans-serif;
}

.fa-check {
	display: none;
	padding: 8px;
}

.active {
	animation: at-click 0.25s ease 0s 2 normal none running;

	box-shadow: $secondary-color 0 0 0 2px inset;

	.score {
		border-color: $secondary-color;
		background-color: $secondary-color;
		color: white;
	}

	.fa-check {
		display: inline-block;
	}
}
</style>
