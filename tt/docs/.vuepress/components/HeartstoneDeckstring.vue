<template lang="pug">
.container(v-cloak, v-if="ready")
  h1.my-3.text-center 爐石讀取牌組代碼
  .form-group
    .form-check.form-check-inline(v-for="tmp in 3", :key="tmp")
      input.form-check-input(type="radio", :value="tmp", name="card-col", :id="`card-col-${tmp}`", v-model="i.cardCol")
      label.form-check-label(:for="`card-col-${tmp}`") 顯示成 {{ tmp }} 列
  .form-group
    label(for="deck-name") 牌組代碼 (每行一個牌組，代碼在前，名稱在後，以空格分隔)
    textarea.text-monospace.deckstrings.form-control(v-model="i.deckstrings")
  .btn-group(role="group")
    button.btn.btn-outline-primary(type="button", v-for="tmp in deckstrings.length", :key="tmp", @click="i.deckNo = tmp", :class="{active: (tmp === i.deckNo)}") {{ tmp }}
  .row.my-2(:style="{ 'max-width': `${i.cardCol * 227 + 30}px`, position: 'relative' }")
    .col-12
      .card-col(v-for="col in _.chunk(cards, Math.ceil(cards.length / i.cardCol))")
        .card-tile(v-for="card in col", :key="`${card.dbfId}-${card.num}`", :style="{'background-image': `linear-gradient(to left, rgba(0,0,0,0) 30%, rgba(0,0,0,1.0) 80%), url('https://art.hearthstonejson.com/v1/tiles/${card.id}.jpg')`}")
          .gem(:class="`gem-${card.rarity}`") {{ card.cost }}
          .name {{ card.name }}
          .count(v-if="card.num>1") {{ card.num }}
          .count(v-if="card.rarity===0") ★
    .col-12
      .deck-name {{ deckName }}
    .screenshots
  textarea.form-control.my-3(readonly, @click="copy", ref="forcopy", :value="`${deckName}\n${deckstring}`")
</template>

<script>
import axios from 'axios'
const RARITY_ORDER = {
  LEGENDARY: 0,
  EPIC: 1,
  RARE: 2,
  FREE: 3,
  COMMON: 4
}

function readVarLen(str) {
  var buf = atob(str);
  var arr = [], len = buf.length, counter = 0, shift, b, res;

  while (counter < len) {
    shift = 0;
    res = 0;
    do {
      if (counter >= len ) {
        throw new RangeError('無法解碼varint');
      }
      b = buf.charCodeAt(counter++);
      res |= (b & 0x7F) << shift;
      shift += 7;
    } while (b >= 0x80);
    arr.push(res);
  }
  return arr;
}

export default {
  data: () => ({
    i: {
      deckstrings: '',
      cardCol: 2,
      deckNo: 1,
    },
    db: null,
    ready: false,
  }),
  async mounted () {
    try {
      let saved = JSON.parse(localStorage.getItem('WNNvKyd'))
      if (saved) this.$set(this, 'i', {...this.i, ...saved})
    } catch (err) {}
    this.$watch('i', (newVal, oldVal) => {
      localStorage.setItem('WNNvKyd', JSON.stringify(this.i))
    }, { deep: true })
    this.db = await this.getHearthstoneDB()
    this.ready = true
  },
  watch: {
    deckName (newVal, oldVal) {
      if (newVal === oldVal) return
      document.title = `${_.padStart(this.i.deckNo, 2, '0')} ${newVal}`
    },
  },
  computed: {
    deckstrings () {
      const lines = _.split(this.i.deckstrings, '\n')
      return _.map(lines, line => {
        const [deck, ...name] = _.split(line, ' ')
        return {
          deck,
          name: name.join(' ')
        }
      })
    },
    deckNo () {
      return _.inRange(this.i.deckNo - 1, this.deckstrings.length + 1) ? this.i.deckNo : 1
    },
    deckstring () {
      return _.get(this, ['deckstrings', this.deckNo - 1, 'deck'])
    },
    deckName () {
      return _.get(this, ['deckstrings', this.deckNo - 1, 'name'])
    },
    deck () {
      return this.deckDecode(this.deckstring)
    },
    hero () {
      if (!_.hasIn(this, 'deck.heroes.0')) return null
      return  _.get(this, ['db', this.deck.heroes[0]])
    },
    cards () {
      if (!_.hasIn(this, 'deck.cards')) return null
      return _.sortBy(_.map(this.deck.cards, ([dbfId, num]) => ({
        ...this.db[dbfId],
        num
      })), ['cost', 'name'])
    },
    format () {
      if (!_.hasIn(this, 'deck.format')) return null
      return ['', '開放', '標準'][this.deck.format]
    },
  },
  methods: {
    async getHearthstoneDB () {
      const res = await axios.get('https://api.hearthstonejson.com/v1/latest/zhTW/cards.collectible.json')
      const cards = _.map(_.get(res, 'data', []), card => ({
        ..._.pick(card, ['id', 'dbfId', 'name', 'cost']),
        rarity: _.get(RARITY_ORDER, card.rarity, 5)
      }))
      return _.mapKeys(cards, card => card.dbfId)
    },
    deckDecode (deckstring) {
      let arr = readVarLen(deckstring);
      let idx = 3;

      let format = arr[2];
      let heroes = new Array(arr[idx++]);
      for (let i = 0; i < heroes.length; i++) {
        heroes[i] = arr[idx++];
      }
      let cards = [];
      for (let i = 1; i <= 3; i++) {
        for (var j = 0, c = arr[idx++]; j < c; j++) {
          cards.push([
            arr[idx++],
            i === 1 || i === 2 ? i : arr[idx++],
          ]);
        }
      }
      return { format, heroes, cards }
    },
    copy () {
      const forcopy = this.$refs.forcopy
      console.log(forcopy)
      forcopy.select()
      forcopy.setSelectionRange(0, 99999)
      document.execCommand("copy")
    },
  }
}
</script>

<style lang="sass" scoped>
  body
    font-family: SFMono-Regular, Menlo, Monaco, "Liberation Mono", "Courier New", monospace, 'Noto Sans TC', sans-serif
  [v-cloak]
    display: none
  .deckstrings
    font-size: 0.75rem
    min-height: 150px
  .card-col
    width: 227px
    float: left
  .card-tile
    width: 226px
    height: 32px
    line-height: 32px
    display: inline-block
    position: relative
    vertical-align: top

    box-sizing: border-box
    border: 1px solid #000
    margin-top: 1px
    margin-left: 1px

    color: #fff
    font-weight: 700
    text-shadow: 0px 0px 2px black, 0px 0px 2px black, 0px 0px 2px black, 0px 0px 2px black

    background-color: #1d1f20
    background-position: right center
    background-repeat: no-repeat
    .name
      width: 160px
      height: 100%
      margin-left: 5px
      float: left
      overflow: hidden
      text-overflow: ellipsis
      white-space: nowrap
      text-align: left
      font-size: 15px
      font-weight: 300
      font-family: 'Noto Sans TC', sans-serif
      text-shadow: 0px 0px 3px black, 0px 0px 3px black, 0px 0px 3px black, 0px 0px 5px black
    .count
      width: 23px
      height: 100%
      float: right

      background-color: #313131
      border-left: 1px solid #000

      color: #f4d442
      text-align: center
      font-size: 17px
    .gem
      font-family: Helvetica, Noto Sans, sans-serif
      width: 32px
      height: 100%
      float: left

      background: black
      border-right: 1px solid #000

      font-size: 19px
      text-align: center
    .gem-0
      background: #904000
    .gem-1
      background: #8a2c79
    .gem-2
      background: #315376
    .gem-3
      background: #555555
    .gem-4
      background: #757575
  .screenshots
    position: absolute
    top: 0
    bottom: 0
    left: 15px
    right: 15px
</style>
