<template lang="pug">
  .two-sections-view#two-sections-containers-example-view
    .left-subview-box.scrollable-section(style="background: #5BAE8C;")
      .scrollable-box
        title-box(:icon="$helpers.getRouteIcon(true)" :title="$helpers.getRouteTitle()")
        .lorem-ipsum-text(v-html="example_text")
    .right-subview-box.scrollable-section(style="background: #1B5467; color: #DDD")
      .scrollable-box
        title-box(:icon="$helpers.getRouteIcon(true)" :title="$helpers.getRouteTitle()")
        .lorem-ipsum-text(v-html="example_text")
        .lorem-ipsum-text(v-html="example_text")
</template>

<script>
  export default {
    data() {
      return {
        example_text: '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin aliquam eros in lorem sodales, nec fringilla sem consectetur. Etiam nec consectetur eros. In auctor augue sem, a varius metus feugiat iaculis. Duis vitae tellus lectus. Vestibulum sodales est id condimentum porttitor. Nunc dictum nisl sit amet nibh vestibulum ultrices. In imperdiet, velit sit amet sodales suscipit, magna lorem convallis ex, vitae luctus nisi metus in nulla. Suspendisse malesuada ante id tortor pharetra interdum.</p><p>Aliquam ut dolor augue. Nunc eget velit quis velit consequat tincidunt. Pellentesque aliquet tristique ipsum, sed commodo diam. Nunc dignissim est bibendum viverra posuere. Nunc tempus dolor eget justo ornare, et porttitor ligula ullamcorper. Nullam at risus turpis. Curabitur sit amet bibendum lectus, quis rhoncus dui. Mauris sit amet justo id leo dignissim rutrum. Vivamus condimentum volutpat quam, eu iaculis dui pretium eget. Sed nec nisi vitae massa ornare porta.</p><p>Phasellus cursus arcu nec est bibendum, eget aliquet dolor vulputate. Phasellus pretium eros luctus bibendum efficitur. Aliquam dolor orci, sodales non aliquet id, imperdiet non nibh. Phasellus vestibulum ullamcorper lectus et sodales. Duis justo odio, hendrerit elementum lacus id, maximus egestas neque. Quisque venenatis nisl quis neque consectetur gravida. Pellentesque hendrerit dictum mi, non laoreet nunc. Nulla dignissim quam nec nunc dapibus, sed maximus orci convallis. Sed nisi lectus, mollis quis condimentum nec, ornare elementum tellus.</p><p>Pellentesque eget mi luctus urna condimentum consectetur. Maecenas interdum tellus et neque laoreet, hendrerit consequat enim lobortis. Aliquam lacinia et ex non lacinia. Duis laoreet leo sed augue fermentum maximus. Integer in commodo metus. Suspendisse ut diam venenatis nibh laoreet luctus. Phasellus condimentum quam purus, in interdum neque maximus a. Nam faucibus dui non quam congue, in vehicula felis molestie. Sed non egestas lectus.<p><p> Phasellus libero orci, pulvinar eu condimentum non, sodales ut tortor. Vivamus mollis leo at fermentum malesuada. Pellentesque posuere vulputate odio tristique luctus. Morbi mattis nisl quis iaculis venenatis. Aliquam velit purus, suscipit non est non, dapibus placerat nunc. Phasellus at odio ante. Suspendisse potenti. Ut quis scelerisque neque.</p><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin aliquam eros in lorem sodales, nec fringilla sem consectetur. Etiam nec consectetur eros. In auctor augue sem, a varius metus feugiat iaculis. Duis vitae tellus lectus. Vestibulum sodales est id condimentum porttitor. Nunc dictum nisl sit amet nibh vestibulum ultrices. In imperdiet, velit sit amet sodales suscipit, magna lorem convallis ex, vitae luctus nisi metus in nulla. Suspendisse malesuada ante id tortor pharetra interdum.</p>'
      }
    }
  }
</script>

<style lang="sass" scoped>
</style>
