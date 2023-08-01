<script lang="ts" setup>
import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/20/solid';
import { computed } from 'vue';

interface Props {
	totalPages: number;
	currentPage: number;
	showingPerPage: number;
	results: number;
}
const props = defineProps<Props>();
const emit = defineEmits(['page-click']);

const showingFrom = computed(() => props.showingPerPage * props.currentPage + 1);
const showingTo = computed(() => Math.min(props.showingPerPage * (props.currentPage + 1), props.results));

const handleClick = (action: number | 'prev' | 'next'): void => {
	let page: number;
	if (action === 'next') {
		// if we already are at the last page do nothing
		if (props.currentPage === props.totalPages - 1) return;
		page = props.currentPage + 1;
	} else if (action === 'prev') {
		//if we already are at the first page do nothing
		page = props.currentPage - 1;
		if (props.currentPage === 0) return;
	} else page = action - 1;
	emit('page-click', page);
};
</script>

<template>
	<div class="flex items-center justify-between border-t border-gray-20 px-4 py-3 sm:px-6">
		<div
			v-if="totalPages > 1"
			class="flex flex-1 justify-between sm:hidden"
		>
			<div
				@click="handleClick('prev')"
				class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
			>
				Precedente
			</div>
			<div
				@click="handleClick('next')"
				class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
			>
				Successiva
			</div>
		</div>
		<div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
			<div>
				<p class="text-sm text-gray-700">
					Mostrando da
					<span class="font-medium">{{ showingFrom }}</span>
					a
					<span class="font-medium">{{ showingTo }}</span>
					di
					<span class="font-medium">{{ results }}</span>
					risultati
				</p>
			</div>
			<div>
				<nav
					v-if="totalPages > 1"
					class="isolate inline-flex -space-x-px rounded-md shadow-sm"
				>
					<div
						@click="handleClick('prev')"
						role="button"
						class="relative inline-flex items-center rounded-l-md px-2 py-2 bg-white text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0"
					>
						<ChevronLeftIcon class="h-5 w-5" />
					</div>
					<!-- PAGINATION IF LENGTH < 7 -->
					<div v-if="totalPages < 7">
						<div
							v-for="n in totalPages"
							:key="n"
							role="button"
							@click="handleClick(n)"
							:class="[n === currentPage + 1 ? ['bg-sky-100', 'hover:bg-sky-200', 'text-sky-700'] : ['bg-white', 'hover:bg-gray-50', 'text-gray-900']]"
							class="relative inline-flex items-center px-4 py-2 text-sm font-semibold ring-1 ring-inset ring-gray-300 focus:z-20 focus:outline-offset-0 select-none"
						>
							{{ n }}
						</div>
					</div>
					<!-- PAGINATION IF LENGTH > 7 -->
					<div v-else>
						<!-- link to first page -->
						<div
							v-if="currentPage !== 0"
							role="button"
							@click="handleClick(1)"
							:class="[1 === currentPage + 1 ? ['bg-sky-100', 'hover:bg-sky-200', 'text-sky-700'] : ['bg-white', 'hover:bg-gray-50', 'text-gray-900']]"
							class="relative inline-flex items-center px-4 py-2 text-sm font-semibold ring-1 ring-inset ring-gray-300 focus:z-20 focus:outline-offset-0 select-none"
						>
							1
						</div>
						<span
							v-if="currentPage > 2"
							class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700 ring-1 ring-inset ring-gray-300 focus:outline-offset-0"
							>...</span
						>
						<div
							v-if="currentPage > 1"
							role="button"
							@click="handleClick(currentPage)"
							:class="[1 === currentPage + 1 ? ['bg-sky-100', 'hover:bg-sky-200', 'text-sky-700'] : ['bg-white', 'hover:bg-gray-50', 'text-gray-900']]"
							class="relative inline-flex items-center px-4 py-2 text-sm font-semibold ring-1 ring-inset ring-gray-300 focus:z-20 focus:outline-offset-0 select-none"
						>
							{{ currentPage }}
						</div>
						<div
							role="button"
							class="relative bg-sky-100 hover:bg-sky-200 text-sky-700 inline-flex items-center px-4 py-2 text-sm font-semibold ring-1 ring-inset ring-gray-300 focus:z-20 focus:outline-offset-0 select-none"
						>
							{{ currentPage + 1 }}
						</div>
						<div
							v-if="currentPage + 1 < totalPages"
							role="button"
							@click="handleClick(currentPage + 2)"
							:class="[1 === currentPage + 2 ? ['bg-sky-100', 'hover:bg-sky-200', 'text-sky-700'] : ['bg-white', 'hover:bg-gray-50', 'text-gray-900']]"
							class="relative inline-flex items-center px-4 py-2 text-sm font-semibold ring-1 ring-inset ring-gray-300 focus:z-20 focus:outline-offset-0 select-none"
						>
							{{ currentPage + 2 }}
						</div>
						<span
							v-if="currentPage < totalPages - 3"
							class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700 ring-1 ring-inset ring-gray-300 focus:outline-offset-0"
							>...</span
						>
						<!-- link to last page -->
						<div
							v-if="currentPage + 2 < totalPages"
							role="button"
							@click="handleClick(totalPages)"
							:class="[totalPages === currentPage + 1 ? ['bg-sky-100', 'hover:bg-sky-200', 'text-sky-700'] : ['bg-white', 'hover:bg-gray-50', 'text-gray-900']]"
							class="relative inline-flex items-center px-4 py-2 text-sm font-semibold ring-1 ring-inset ring-gray-300 focus:z-20 focus:outline-offset-0 select-none"
						>
							{{ totalPages }}
						</div>
					</div>

					<div
						@click="handleClick('next')"
						role="button"
						class="relative inline-flex items-center rounded-r-md px-2 py-2 bg-white text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0"
					>
						<ChevronRightIcon class="h-5 w-5" />
					</div>
				</nav>
			</div>
		</div>
	</div>
</template>
