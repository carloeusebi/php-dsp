<script lang="ts" setup>
import AppTableHead from './AppTableHead.vue';

import { Cell, Patient, Survey } from '@/assets/data/interfaces';

interface Props {
	cells: Cell[];
	hasReset?: boolean;
	canSort?: boolean;
}

withDefaults(defineProps<Props>(), {
	hasReset: () => true,
	canSort: () => true,
});

const emit = defineEmits(['sort-change']);

const handle = (newOrder: keyof Patient | keyof Survey) => {
	emit('sort-change', newOrder);
};
</script>

<template>
	<div class="w-full mb-8 overflow-hidden rounded-lg shadow-lg">
		<div class="w-full overflow-x-auto">
			<table class="w-full">
				<!-- TABLE HEAD -->
				<AppTableHead
					@sort-change="handle"
					:cells="cells"
					:has-reset="hasReset"
					:can-sort="canSort"
				/>

				<!-- TBODY -->
				<tbody class="bg-white">
					<slot name="tbody"></slot>
				</tbody>
			</table>
		</div>
	</div>
</template>
