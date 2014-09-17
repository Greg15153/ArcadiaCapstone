using System;
using System.Collections.Generic;
using System.Data;
using System.Data.Entity;
using System.Data.Entity.Infrastructure;
using System.Linq;
using System.Net;
using System.Net.Http;
using System.Web.Http;
using System.Web.Http.Description;
using API.Models;
using API.Controllers.Helpers;
using Newtonsoft.Json.Linq;

namespace API.Controllers
{
    public class UsersController : ApiController
    {
        private DB db = new DB();
        private Authorizer auth = new Authorizer();
        private dynamic result = new JObject();
       
        //
        // TO DO: Create way that authorizes ApiKey && Create a way to generate an API Key
        //
        
        // GET: index
        public IHttpActionResult Get()
        {
            result.Add("Status", 501);
            result.Add("Message", "Not implemented");

            return Json(result);
        }

        // GET: api/Users/5
        [HttpGet]
        public IHttpActionResult GetUser(int id, string ApiKey = "")
        {

            dynamic oAuth = auth.authorize("Admin", ApiKey);

            if (oAuth.Status == 401)
                return Json(oAuth);


            User user = db.Users.Find(id);

            if (user == null)
            {
                result.Add("Status", 404);
                result.Add("Message", "User not found");
                return Json(result);
            }

            result.Add("Status", 200);
            
            result.User = new JObject();

            result.User.Add("Id", user.Id);
            result.User.Add("Username", user.Username);
            result.User.Add("Admin", user.Admin);
            result.User.Add("Subject", user.Subject);
            result.User.Add("ApiKey", user.ApiKey.Key);

            return Json(result);
        }

        [ResponseType(typeof(void))]
        [HttpPost]
        public IHttpActionResult SignIn(User user)
        {
            if (!UserExists(user.Username))
            {
                result.Add("Status", 404);
                result.Add("Message", "User not found");
                return Json(result);
            }

            user.Password = Secure.encryptPass(user.Username, user.Password);

                if (db.Users.Count(o => o.Username == user.Username && o.Password == user.Password) > 0)
                {
                    User userInfo = db.Users.Where(o => o.Username == user.Username).Single();
                    result.Add("Status", 200);
                    result.User = new JObject();

                    result.User.Add("Id", userInfo.Id);
                    result.User.Add("Username", userInfo.Username);
                    result.User.Add("Admin", userInfo.Admin);
                    result.User.Add("Subject", userInfo.Subject);
                    result.User.Add("ApiKey", userInfo.ApiKey.Key);

                    return Json(result);
                }


            result.Add("Status", 422);
            result.Add("Message", "Incorrect password");
            return Json(result);
        }

        // Registers new member, requires all details
        // POST: api/Users
        [ResponseType(typeof(void))]
        [HttpPost]
        public IHttpActionResult RegisterUser(User user, String ApiKey)
        {
            dynamic oAuth = auth.authorize("Admin", ApiKey);

            if (oAuth.Status == 401)
                return Json(oAuth);

            if (UserExists(user.Username)) //Checks if username exists
            {
                result.Add("Status", 409);
                result.Add("Message", "Username already exists");
                return Json(result);
            }

           //Check password then encrypt
           user.Password = Secure.encryptPass(user.Username, user.Password);

           if (user.Password == "Too short")
           {
               result.Add("Status", 409);
               result.Add("Message", "Password is too short");
               return Json(result);
           }

           //Generate API Key
            db.Users.Add(user);
            db.SaveChanges();

            ApiKey newKey = new ApiKey { UserId = user.Id, Key = "TomatoJuice"};

            db.ApiKeys.Add(newKey);
            db.SaveChanges();

            result.Add("Status", 201);
            result.Add("Message", "User successfully created");
            return Json(result);
        }

        //Updates user, requires all fields to be filled. All will be updated even if not changed
        // PUT: api/Users/5
        [ResponseType(typeof(void))]
        [HttpPut]
        public IHttpActionResult PutUser(int id, User user, String ApiKey = "")
        {
            dynamic oAuth = auth.authorize("Admin", ApiKey);

            if (oAuth.Status == 401)
                return Json(oAuth);


            if (!ModelState.IsValid)
            {
                
                return BadRequest(ModelState);
            }

            if (id != user.Id)
            {
                result.Add("Status", 400);
                result.Add("Message", "Attempting to update wrong user.");
                return Json(result);
            }

            db.Entry(user).State = EntityState.Modified;

            try
            {
                db.SaveChanges();
            }
            catch (DbUpdateConcurrencyException)
            {
                if (!UserExists(id))
                {
                    result.Add("Status", 404);
                    result.Add("Message", "User not found");
                    return Json(result);
                }
                else
                {
                    throw;
                }
            }

            result.Add("Status", 200);
            result.Add("Message", "User modified succesfully");
            return Json(result);
        }

        protected override void Dispose(bool disposing)
        {
            if (disposing)
            {
                db.Dispose();
            }
            base.Dispose(disposing);
        }

        private bool UserExists(int id)
        {
            return db.Users.Count(e => e.Id == id) > 0;
        }

        private bool UserExists(String username)
        {
            return db.Users.Count(e => e.Username == username) > 0;
        }

    }
}