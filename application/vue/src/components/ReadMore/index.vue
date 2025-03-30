<template lang="pug">
  .read-more-text-box
    .contenido(lang="es" ref="contenido")
      span(ref="text_box")
        .fake-div-to-cheat-on-chrome(v-html="text")
    .read-less-more-fake-button-box(v-if="has_more_text_to_expand")
      .read-less-more-fake-button(v-if="read_more_color" :style="{ color: read_more_color}" @click="mostrarInfo") {{!mostrarContenidoCompleto ? read_more_text : read_less_text}}
      .read-less-more-fake-button(v-else @click="mostrarInfo") {{ (!mostrarContenidoCompleto) ? read_more_text : read_less_text }}
</template>

<script>
  export default {
    name: 'AddNewThing',
    props: {
      text: {
        type: String
      },
      line_clamp: {
        type: Number,
        default: 1
      },
      initial_line_clamp_px: {
        type: Number,
        default: 19
        // por cada línea, lo normal son unos 19px apróx.
      },
      read_more_text: {
        type: String,
        default: 'Leer más'
      },
      read_less_text: {
        type: String,
        default: 'Mostrar menos'
      },
      load_on_true: {
        type: Boolean,
        default: true
      },
      read_more_color: {
        type: String,
        default: null
      }
    },
    mounted() {
      this.is_component_mounted = true
      this.$refs.contenido.style.height = `${this.initial_line_clamp_px}px`
      this.$refs.contenido.style['-webkit-line-clamp'] = this.line_clamp
      window.addEventListener('resize', this.textBoxResized)

      // Para comparar más adelante, se guarda porque al expandirse cambiará su valor, pero queremos guardar su valor inicial
      this.offset_height_contenido = this.$refs.contenido.offsetHeight

      window.dispatchEvent(new Event('resize'))
    },
    beforeDestroy() {
      window.removeEventListener('resize', this.textBoxResized)
    },
    data() {
      return {
        offset_height_contenido: 500,
        mostrarContenidoCompleto: false,
        is_first_time: true,
        is_component_mounted: false
      }
    },
    methods: {
      textBoxResized(event) {
        this.is_component_mounted = false
        setTimeout(() => {
          if (this.is_first_time) {
            this.offset_height_contenido = this.$refs.contenido.offsetHeight
            this.is_first_time = false
          }
          this.is_component_mounted = true
        }, 10)
      },
      mostrarInfo() {
        if (!this.mostrarContenidoCompleto) {
          const text_box_height = this.$refs.text_box.firstChild.offsetHeight
          this.$refs.contenido.style.height = text_box_height + 'px'
          this.$refs.contenido.style.display = 'inherit'
        } else {
          this.$refs.contenido.style.height = `${this.initial_line_clamp_px}px`
          setTimeout(() => {
            this.$refs.contenido.style.display = '-webkit-box'
          }, 300)
        }
        this.mostrarContenidoCompleto = !this.mostrarContenidoCompleto
      }
    },
    computed: {
      has_more_text_to_expand() {
        return (this.is_component_mounted) ? (this.$refs.text_box.firstChild.offsetHeight > this.offset_height_contenido) : false
      }
    }
  }
</script>

<style lang="sass" scoped>
  .read-more-text-box
    .contenido
      overflow: hidden
      display: -webkit-box
      -webkit-line-clamp: 1
      -webkit-box-orient: vertical
      text-align: start
      transition: 0.3s
      height: 19px
      word-break: normal !important
      text-align: justify !important
      hyphens: auto !important
    .contenido /deep/
      p
        margin: 0px !important
    .read-less-more-fake-button-box
      // text-align: right
      font-style: italic
      display: flex
      justify-content: flex-end
      color: var(--main-color)
      .read-less-more-fake-button
        cursor: pointer
        margin: 5px 0px 10px 0px
        transition: .3s
        font-size: 15px
        &:hover
          opacity: 0.6
          transition: .3s

</style>
