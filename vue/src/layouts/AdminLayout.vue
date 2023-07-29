<script lang="ts" setup>
import TheNavbar from '@/components/TheNavbar.vue';
</script>

<template>
	<header>
		<TheNavbar />
	</header>
	<main>
		<router-view></router-view>
	</main>
</template>

<style scoped>
::-webkit-scrollbar {
	width: 0px;
}
</style>
