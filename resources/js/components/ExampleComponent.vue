<template>
  <div class="container-new clearfix">
    <div class="people-list" id="people-list">
      <div class="search">
        <input type="text" placeholder="search" />
        <i class="fa fa-search"></i>
      </div>
      <ul class="list">
        <li
          @click.prevent="singleUser(user.id)"
          class="clearfix"
          v-for="(user, index) in getUsers"
          :key="index"
        >
          <!-- <img
            src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/195612/chat_avatar_01.jpg"
            alt="avatar"
          />-->
          <div class="about">
            <div class="name">{{ user.name }}</div>
            <div class="status"><i class="fa fa-circle online"></i> online</div>
          </div>
        </li>
      </ul>
    </div>

    <div class="chat">
      <div class="chat-header clearfix">
        <img
          src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/195612/chat_avatar_01_green.jpg"
          alt="avatar"
        />

        <div class="chat-about">
          <div v-if="getUserMessages.user" class="chat-with">
            {{ getUserMessages.user.name }}
          </div>
          <div class="dropdown float-left">
            <a
              href="#"
              class="ml-1 dropdown-toggle"
              id="dropdownMenu2"
              data-toggle="dropdown"
              aria-haspopup="true"
              aria-expanded="false"
              style="text-decoration: none"
              >...</a
            >
            <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
              <button
                @click.prevent="deleteAllMessage"
                class="dropdown-item"
                type="button"
              >
                delete all message
              </button>
            </div>
          </div>
          <!-- <div class="chat-num-messages">already 1 902 messages</div> -->
        </div>
        <i class="fa fa-star"></i>
      </div>
      <!-- end chat-header -->

      <div class="chat-history">
        <ul>
          <li
            class="clearfix"
            v-for="(message, index) in getUserMessages.userMessages"
            :key="index"
          >
            <div class="message-data align-right">
              <span class="message-data-time">10:10 AM, Today</span> &nbsp;
              &nbsp;
              <span class="message-data-name">{{ message.user.name }}</span>

              <div class="dropdown float-right">
                <a
                  href="#"
                  class="ml-1 dropdown-toggle"
                  id="dropdownMenu2"
                  data-toggle="dropdown"
                  aria-haspopup="true"
                  aria-expanded="false"
                  style="text-decoration: none"
                  >...</a
                >
                <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                  <button
                    @click.prevent="deleteSingleMessage(message.id)"
                    class="dropdown-item"
                    type="button"
                  >
                    delete message
                  </button>
                </div>
              </div>

              <i class="fa fa-circle me"></i>
            </div>
            <div
              :class="`message float-right ${
                message.user.id == getUserMessages.user.id
                  ? 'other-message'
                  : 'my-message'
              }`"
            >
              {{ message.message }}
            </div>
          </li>
        </ul>
      </div>
      <!-- end chat-history -->

      <div class="chat-message clearfix">
        <textarea
          name="message-to-send"
          id="message-to-send"
          placeholder="Type your message"
          rows="3"
          v-model="message_text"
          @keydown.enter="sendMessage"
        ></textarea>

        <i class="fa fa-file-o"></i> &nbsp;&nbsp;&nbsp;
        <i class="fa fa-file-image-o"></i>

        <button @click="sendMessage">Send</button>
      </div>
      <!-- end chat-message -->
    </div>
    <!-- end chat -->
  </div>
  <!-- end container -->
</template>

<script>
export default {
  mounted() {
    Echo.private(`chat.${authUser.id}`).listen("MessageSend", (e) => {
      this.singleUser(e.message.user_id);
    });
    this.$store.dispatch("getUsers");
  },
  data() {
    return {
      message_text: "",
    };
  },
  computed: {
    getUsers() {
      return this.$store.getters.getUsers;
    },
    getUserMessages() {
      return this.$store.getters.getUserMessages;
    },
  },
  methods: {
    singleUser(userId) {
      this.$store.dispatch("getUserMessages", userId);
    },
    sendMessage(e) {
      e.preventDefault();
      if (this.message_text != "" && this.getUserMessages.user.id != "") {
        axios
          .post("/send-message", {
            message: this.message_text,
            receiver: this.getUserMessages.user.id,
          })
          .then((response) => this.singleUser(this.getUserMessages.user.id));
        this.message_text = "";
      }
    },
    deleteSingleMessage(id) {
      axios
        .get("/delete-single-message/" + id)
        .then((response) => this.singleUser(this.getUserMessages.user.id));
    },
    deleteAllMessage() {
      axios
        .get("/delete-all-message/" + this.getUserMessages.user.id)
        .then((response) => this.singleUser(this.getUserMessages.user.id));
    },
  },
};
</script>