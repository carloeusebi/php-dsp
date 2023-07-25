<script lang="ts" setup>
import { computed } from 'vue';

const props = defineProps({
	show: Boolean,
	type: String,
	title: String,
	message: String,
	padding: {
		type: Number,
		default: 6,
	},
	duration: Number,
	transition: {
		type: Boolean,
		default: true,
	},
});

const className = computed((): string => {
	let className: string;
	switch (props.type) {
		case 'warning':
			className = 'orange';
			break;
		case 'success':
			className = 'teal';
			break;
		default:
			className = 'blue';
	}
	return className;
});
</script>

<template>
	<Transition :css="transition">
		<div
			v-if="show"
			:class="`top-10 left-${padding} right-${padding} bg-${className}-100 border-l-4 border-${className}-500 text-${className}-700 p-4`"
			role="alert"
		>
			<p
				v-if="title"
				class="font-bold"
			>
				{{ title }}
			</p>
			<slot></slot>
			<span v-html="message"></span>
		</div>
	</Transition>
</template>

<style scoped>
.v-leave-active {
	transition: opacity 0.5s ease;
}

.v-leave-to {
	opacity: 0;
}
</style>
