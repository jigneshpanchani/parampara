<template>
  <!-- pagination-start -->
  <nav aria-label="Page navigation example">
    <ul class="pagination pagination-sm float-right mb-0">
      <li
        class="page-item"
        :class="{ 'not-allowed disabled': pagination.current_page <= 1 }"
      >
        <a
          v-tooltip="'First'"
          class="page-link"
          tabindex="-1"
          @click.prevent="changePage(1)"
        >
          <i class="fas fa-angle-double-left"></i>
        </a>
      </li>
      <li
        class="page-item"
        :class="{ 'not-allowed disabled': pagination.current_page <= 1 }"
      >
        <a
          v-tooltip="'Prev'"
          href="#"
          class="page-link"
          :class="{ 'not-allowed': pagination.current_page <= 1 }"
          @click.prevent="changePage(pagination.current_page - 1)"
        >
          <i class="fas fa-angle-left"></i>
        </a>
      </li>
      <li
        v-for="page in pages"
        :key="page"
        class="page-item"
        :class="{ 'not-allowed disabled currentpage': isCurrentPage(page) }"
      >
        <a href="#" class="page-link" @click.prevent="changePage(page)">
          {{ page }}
        </a>
      </li>
      <li
        class="page-item"
        :class="{
          'not-allowed disabled':
            pagination.current_page >= pagination.last_page,
        }"
      >
        <a
          v-tooltip="'Next'"
          href="#"
          class="page-link"
          @click.prevent="changePage(pagination.current_page + 1)"
        >
          <i class="fas fa-angle-right"></i>
        </a>
      </li>
      <li
        class="page-item"
        :class="{
          'not-allowed disabled':
            pagination.current_page >= pagination.last_page,
        }"
      >
        <a
          v-tooltip="'Last'"
          href="#"
          class="page-link"
          @click.prevent="changePage(pagination.last_page)"
        >
          <i class="fas fa-angle-double-right"></i>
        </a>
      </li>
    </ul>
  </nav>
  <!-- pagination-end -->
</template>

<script>
import { ref, computed } from 'vue';

export default {
  name: 'Pagination',
  props: {
    pagination: {
      type: Object,
      default: null,
    },
    offset: {
      type: Number,
      default: null,
    },
  },

  setup(props, { emit }) {
    const isCurrentPage = (page) => props.pagination.current_page === page;
    const changePage = (page) => {
      if (page > props.pagination.last_page) {
        page = props.pagination.last_page;
      }
      emit('paginate', page);
    };

    const pages = computed(() => {
      const pagesArray = [];
      let from = props.pagination.current_page - Math.floor(props.offset / 2);
      if (from < 1) {
        from = 1;
      }
      let to = from + props.offset - 1;
      if (to > props.pagination.last_page) {
        to = props.pagination.last_page;
      }
      while (from <= to) {
        pagesArray.push(from);
        from++;
      }
      return pagesArray;
    });

    return {
      isCurrentPage,
      changePage,
      pages,
    };
  },
};
</script>

<style scoped>
.not-allowed {
  cursor: not-allowed;
}

.not-allowed a {
  background: #ffffff14 !important;
}

.currentpage a {
  background: var(--secondaryColor) !important;
  color: var(--primaryColor) !important;
}

.pagination-sm .page-link {
  padding: 10px 15px;
  font-size: 15px;
  line-height: 1.5;
}

.dark-mode .page-item:not(.active) .page-link {
  background-color: #000000;
  border-color: #6c757d;
}

.dark-mode .page-item .page-link {
  color: #ffffff;
}
</style>
