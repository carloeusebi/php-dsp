<script lang="ts" setup>
defineProps({
	title: String,
});
</script>

<template>
	<header class="mb-8">
		<figure class="flex justify-end">
			<img
				src="/Logo.webp"
				alt="Logo"
			/>
		</figure>
		<h1 class="md:text-3xl font-bold my-10">{{ title }}</h1>
	</header>
</template>

<style scoped>
img {
	max-width: 50%;
	width: 350px;
}
</style>
