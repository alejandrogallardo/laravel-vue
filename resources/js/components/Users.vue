<template>
    <div class="container">
        <div class="row mt-3" v-if="$gate.isAdminOrAuthor()" >
          <div class="col-12">
            <div class="card  mt-3">
              <div class="card-header">
                <h3 class="card-title">Users</h3>

                <div class="card-tools">
                  <!-- data-toggle="modal" data-target="#exampleModalCenter" -->
                    <button class="btn btn-success" @click="newModal">Add new <i class="fas fa-user-plus"></i></button>
                </div>

              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Name</th>
                      <th>Email</th>
                      <th>Type</th>
                      <th>Registered At</th>
                      <th>Modify</th>
                    </tr>
                  </thead>
                  <tbody>

                    <tr v-for="user in users.data" :key="user.id">
                      <td>{{ user.id }}</td>
                      <td>{{ user.name }}</td>
                      <td>{{ user.email }}</td>
                      <td>{{ user.type | upText }}</td>
                      <td>{{ user.created_at | myDate }}</td>
    
                      <td>
                        <a href="#" @click="editModal(user)">
                            Edit
                            <i class="fa fa-edit"></i>
                        </a>
                        <a class="red" href="#" @click="deleteUser(user.id)">
                            Delete
                            <i class="fa fa-trash"></i>
                        </a>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                <pagination :data="users" @pagination-change-page="getResults">
                  <span slot="prev-nav">&lt; Previous</span>
	                <span slot="next-nav">Next &gt;</span>
                </pagination>
              </div>
            </div>
            <!-- /.card -->
          </div>
        </div>

        <div v-if="!$gate.isAdminOrAuthor()">
            <not-found></not-found>
        </div>

    <!-- Modal -->
<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">

        <h5 class="modal-title" v-show="editMode" id="exampleModalLongTitle">Update User's Information</h5>
        <h5 class="modal-title" v-show="!editMode" id="exampleModalLongTitle">New User</h5>
        
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

          <form @submit.prevent="editMode ? updateUser() : createUser()">
      <div class="modal-body">
        <!-- Form -->

            <div class="form-group">
              <input v-model="form.name" type="text" name="name"
              placeholder="Name"
                class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
              <has-error :form="form" field="name"></has-error>
            </div>

            <div class="form-group">
              <input v-model="form.email" type="email" name="email"
              placeholder="Email Address"
                class="form-control" :class="{ 'is-invalid': form.errors.has('email') }">
              <has-error :form="form" field="email"></has-error>
            </div>

            <div class="form-group">
              <textarea v-model="form.bio" name="bio" id="bio"
              placeholder="Short bio for user (optional)"
                class="form-control" :class="{ 'is-invalid': form.errors.has('bio') }"></textarea>
              <has-error :form="form" field="bio"></has-error>
            </div>

            <div class="form-group">
              <select v-model="form.type" name="type" bio="type"
                class="form-control" :class="{ 'is-invalid': form.errors.has('type') }">
                <option value="">Select User Role</option>
                <option value="admin">Admin</option>
                <option value="user">Standar User</option>
                <option value="author">Author</option>
              </select>
              <has-error :form="form" field="type"></has-error>
            </div>

            <div class="form-group">
              <input v-model="form.password" type="password" name="password"
              placeholder="Password"
                class="form-control" :class="{ 'is-invalid': form.errors.has('password') }">
              <has-error :form="form" field="password"></has-error>
            </div>

        <!-- End form -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        <button v-show="editMode" type="submit" class="btn btn-success">Update</button>
        <button v-show="!editMode" type="submit" class="btn btn-primary">Create</button>
      </div>
          </form>

    </div>
  </div>
</div>
<!-- End Modal -->
    </div>

</template>

<script>
    export default {
      data(){
        return {
          editMode: false,
          users: {},
          form: new Form({
            id: '',
            name: '',
            email: '',
            password: '',
            type: '',
            bio: '',
            photo: ''
          })          
        }
      },
      methods: {
        getResults(page = 1) {
          axios.get('api/user?page=' + page)
            .then(response => {
              this.users = response.data;
            });
        },
        updateUser(){
          this.$Progress.start()
          this.form.put('api/user/'+this.form.id)
          .then(() => {
            // Success
            $('#exampleModalCenter').modal('hide')
            Swal.fire({
              title: 'Updated!',
              text: 'Information has been updated!', 
              // 'Success'
              icon: 'success',
              showConfirmButton: false,
              timer: 2000,
            })
          this.$Progress.finish()
          Fire.$emit('AfterCreate');
          })
          .catch(() => {
            this.$Progress.fail()
          })
        },
        editModal(user){
          this.editMode = true;
          this.form.reset();
          $('#exampleModalCenter').modal('show')
          this.form.fill(user)
        },
        newModal(){
          this.editMode = false;
          this.form.reset();
          $('#exampleModalCenter').modal('show')
        },
        deleteUser(id){
          Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
          }).then((result) => {

            // Send request to the server
            if ( result.value ){
            this.form.delete('api/user/'+id).then(() => {
              
                Swal.fire(
                  'Deleted!',
                  'Your file has been deleted.',
                  'success'
                )
              Fire.$emit('AfterCreate');
            }).catch(() => {
              Swal.fire(
                'Failed!',
                'There was something wronge', 
                'warning'
              )
            })
          }
          })
        },
        loadUsers(){
          if( this.$gate.isAdminOrAuthor() ){
            axios.get("api/user").then(({ data }) => (this.users = data ));
          }
        },
        createUser(){
          // Submit the form via a POST request
          this.$Progress.start()
          
          this.form.post('api/user')
          .then(() => {
            Fire.$emit('AfterCreate');

  
            $('#exampleModalCenter').modal('hide')
              
              Toast.fire({
                icon: 'success',
                title: 'Signed in successfully'
              })
  
            this.$Progress.finish()
              // .then(({ data }) => { console.log(data) })

          })
          .catch(() => {

          })

        }
      },
        created() {
          // listeng for the searching
          Fire.$on('searching',() => {
                let query = this.$parent.search;
                axios.get('api/findUser?q=' + query)
                .then((data) => {
                    this.users = data.data
                })
                .catch(() => {
                })
            })
            this.loadUsers();
            Fire.$on('AfterCreate', () => {
              this.loadUsers();
            });
            // setInterval(() => this.loadUsers(), 3000);
        }
    }
</script>