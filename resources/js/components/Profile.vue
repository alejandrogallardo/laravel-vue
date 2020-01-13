<style>
.widget-user-header{
    background-position: center center;
    background-size: cover;
    height: 250px;
}

</style>

<template>
    <div class="container">
        <div class="row">
            <div class="col-md-12 mt-3">
                <div class="card card-widget widget-user">
              <!-- Add the bg color to the header using any of the bg-* classes -->
              <div class="widget-user-header text-white" style="background-image:url('./img/defaultBG.jpg')">
                <h3 class="widget-user-username text-right">{{this.form.name}}</h3>
                <h5 class="widget-user-desc text-right">{{this.form.type | upText }}</h5>
              </div>
              <div class="widget-user-image">
                <img class="img-circle" :src="getProfilePhoto()" alt="User Avatar">
              </div>
              <div class="card-footer">
                <div class="row">
                  <div class="col-sm-4 border-right">
                    <div class="description-block">
                      <h5 class="description-header">3,200</h5>
                      <span class="description-text">SALES</span>
                    </div>
                    <!-- /.description-block -->
                  </div>
                  <!-- /.col -->
                  <div class="col-sm-4 border-right">
                    <div class="description-block">
                      <h5 class="description-header">13,000</h5>
                      <span class="description-text">FOLLOWERS</span>
                    </div>
                    <!-- /.description-block -->
                  </div>
                  <!-- /.col -->
                  <div class="col-sm-4">
                    <div class="description-block">
                      <h5 class="description-header">35</h5>
                      <span class="description-text">PRODUCTS</span>
                    </div>
                    <!-- /.description-block -->
                  </div>
                  <!-- /.col -->
                </div>
                <!-- /.row -->
              </div>
            </div>

            <!--  -->
            <div class="col-md-12">
            <div class="card">
              <div class="card-header p-2">
                <ul class="nav nav-pills">
                  <li class="nav-item"><a class="nav-link active" href="#activity" data-toggle="tab">Activity</a></li>
                  
                  <li class="nav-item"><a class="nav-link" href="#settings" data-toggle="tab">Settings</a></li>
                </ul>
              </div><!-- /.card-header -->

              <div class="card-body">
                <div class="tab-content">
                  <div class="tab-pane active" id="activity">
                    <!-- Post -->
                    <div class="post">
                      <h3>Hola! ;)</h3>
                    </div>
                    <!-- /.post -->

                    

                    
                  </div>
                  <!-- /.tab-pane -->

                  <!-- settings-pane -->

                  <div class="tab-pane" id="settings">
                    <form class="form-horizontal">
                      <div class="form-group">
                        <div class="col-sm-12">
                        <label for="inputName" class="col-sm-12 col-form-label">Name</label>
                          <input  type="email" 
                                  v-model="form.name" 
                                  class="form-control" 
                                  id="inputName" 
                                  placeholder="Name"
                                  :class="{ 'is-invalid': form.errors.has('name') }">
                        </div>
                      </div>
                      <div class="form-group">
                        <div class="col-sm-12">
                        <label for="inputEmail" class="col-sm-12 col-form-label">Email</label>
                          <input  type="email" 
                                  v-model="form.email" 
                                  class="form-control" 
                                  id="inputEmail" 
                                  placeholder="Email"
                                  :class="{ 'is-invalid': form.errors.has('email') }">
                        </div>
                      </div>
                      
                      <div class="form-group">
                        <div class="col-sm-12">
                        <label for="inputExperience" class="col-sm-12 col-form-label">Experience</label>
                          <textarea class="form-control" 
                                    v-model="form.bio" 
                                    id="inputExperience" 
                                    placeholder="Experience"
                                    :class="{ 'is-invalid': form.errors.has('bio') }"></textarea>
                        </div>
                      </div>

                      <div class="form-group">
                        <div class="col-sm-12">
                        <label for="photo" class="col-sm-12 col-form-label">Profile Photo</label>
                          <!-- <button type="file" class="">Chooose file</button> -->
                          <input type="file" @change="updateProfile" name="photo" class="form-input">
                        </div>
                      </div>

                      <div class="form-group">
                          <label for="password" class="col-sm-12 control-label">Passport (leave empty if not changing)</label>

                          <div class="col-sm-12">
                          <input type="password"
                              v-model="form.password"
                              class="form-control"
                              id="password"
                              placeholder="Passport"
                              :class="{ 'is-invalid': form.errors.has('password') }"
                          >
                            <has-error :form="form" field="password"></has-error>
                          </div>
                      </div>
                      
                      <div class="form-group">
                        <div class="col-sm-12">
                          <button type="submit" @click.prevent="updateInfo" class="btn btn-success">Update</button>
                        </div>
                      </div>
                    </form>
                  </div>
                  <!-- /.tab-pane -->
                </div>
                <!-- /.tab-content -->
              </div><!-- /.card-body -->
            </div>
            <!-- /.nav-tabs-custom -->
          </div>

            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data(){
            return {
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
        mounted() {
            console.log('Component mounted.')
        },

        methods : {

          getProfilePhoto(){
                let photo = (this.form.photo.length > 200) ? this.form.photo : "img/profile/"+ this.form.photo ;
                return photo;
            },

          updateInfo(){
            this.$Progress.start();
            this.form.put('api/profile' )
            .then(() => {
                Fire.$emit('AfterCreate');
                this.$Progress.finish();
            })
            .catch(() => {  
              this.$Progress.fail();
            })
          },
          updateProfile( e ){
            // console.log('Uploaded');
            let file = e.target.files[0];
            // console.log( file );
            let reader = new FileReader();

            if ( file['size'] < 2111775 ){
              reader.onloadend = ( file ) => {
                // console.log('RESULT', reader.result)
                this.form.photo = reader.result;
              }
              reader.readAsDataURL(file);
            }else{
            Swal.fire({
                type: 'error',
                title: 'Oops...',
                text: "You are uploading a large file",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
              })
            }

          }
        },
        
        created() {
            axios.get("api/profile")
            .then(({ data }) => (this.form.fill(data)));
        }

    }
</script>