<html>
    <head>
        <title>User Listing</title>
    </head>
    <body>
<div id="app">
           
            <table align="center" border="1" >
             <tr><td colspan="7" align="center">User Listing</td></tr>

			<tr>
					
					<td width="10%"><b>User Id</b></td>
					<td width="27%"><b>User Name</b></td>
					<td width="15%"><b>Designation</b></td>
					<td width="8%"><b>Phone no</b></td>
					<td width="8%"><b>Password</b></td>
					<td width="10%"><b>Role</b></td>
					<td width="8%"><b>status</b></td>
			</tr>
            <tr v-for="item in items">
                    <td>{{ item['fields']['user_id'] }}</td>
                    <td>{{ item['fields']['name'] }}</td>
                    <td>{{ item['fields']['designation'] }}</td>
                    <td>{{ item['fields']['phone_no'] }}</td>
                    <td>{{ item['fields']['password'] }}</td>
                    <td>{{ item['fields']['role'] }}</td>
                    <td>{{ item['fields']['status'] }}</td>
            </tr> 
            </table>
                       
        </div><!--app-->
        
        <!-- Include Dependancy Scripts -->
        <script type="text/javascript" src="https://unpkg.com/vue"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.16.2/axios.min.js"></script>
<script type="text/javascript">
            var app = new Vue({
                el: '#app',
                data: {
                    items: []
                },
                mounted: function(){
                   this.loadItems(); 
                },
                methods: {
                    loadItems: function(){
                        
                        // Init variables
                        var self = this
                        var app_id = "appsNsTgB7wRF9VbC";
                        var app_key = "keycj1akzzBgbVweS";
                        this.items = []
axios.get(
                            "https://api.airtable.com/v0/"+app_id+"/user_master?view=Grid%20view",
                            { 
                                headers: { Authorization: "Bearer "+app_key } 
                            }
                        ).then(function(response){
                            self.items = response.data.records
                        }).catch(function(error){
                            console.log(error)
                        })
                    }
                }
            })
        </script>
    </body>
</html>