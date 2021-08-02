<template lang="pug">
  RouterLink.nav-link(v-if="isInternal", :to="link", :exact="exact", @focusout.native="focusoutAction") #[i.fa.fa-fw(v-if="item.fa", :class="`fa-${item.fa}`")] {{ item.text }}
  a.nav-link.external(v-else, :href="link", :target="target", :rel="rel", @focusout="focusoutAction") #[i.fa.fa-fw(v-if="item.fa", :class="`fa-${item.fa}`")] {{ item.text }} #[OutboundLink(v-if="isBlankTarget")]
</template>

<script>
import { isExternal, isMailto, isTel, ensureExt } from '@parent-theme/util'
export default {
  name: 'NavLink',
  props: {
    item: {
      required: true
    }
  },
  computed: {
    link () {
      return ensureExt(this.item.link)
    },
    exact () {
      if (this.$site.locales) {
        return Object.keys(this.$site.locales).some(rootLink => rootLink === this.link)
      }
      return this.link === '/'
    },
    isNonHttpURI () {
      return isMailto(this.link) || isTel(this.link)
    },
    isBlankTarget () {
      return this.target === '_blank'
    },
    isInternal () {
      return !isExternal(this.link) && !this.isBlankTarget
    },
    target () {
      if (this.isNonHttpURI) {
        return null
      }
      if (this.item.target) {
        return this.item.target
      }
      return isExternal(this.link) ? '_blank' : ''
    },
    rel () {
      if (this.isNonHttpURI) {
        return null
      }
      if (this.item.rel) {
        return this.item.rel
      }
      return this.isBlankTarget ? 'noopener noreferrer' : ''
    }
  },
  methods: {
    focusoutAction () {
      this.$emit('focusout')
    }
  }
}
</script>

<style lang="sass" scoped>
.nav-link
  padding: 0
</style>

