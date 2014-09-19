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
        private dynamic result = new JObject();
       
        //
        // TO DO: Create way that authorizes ApiKey && Create a way to generate an API Key
        //
        
        // GET: index
        public IHttpActionResult Get()
        {
            return StatusCode(HttpStatusCode.NotImplemented);
        }

        // GET: api/Users/5
        [HttpGet]
        public IHttpActionResult GetUser(int id, string ApiKey = "")
        {

            if (Authorizer.authorize("Admin", ApiKey) == 401)
                return Unauthorized();

            User user = db.Users.Find(id);

            if (user == null)
                return NotFound();

            result.Add("Id", user.Id);
            result.Add("Username", user.Username);
            result.Add("Admin", user.Admin);
            result.Add("Subject", user.Subject);
            result.Add("ApiKey", user.ApiKey.Key);

            return Ok(result);
        }

        [ResponseType(typeof(void))]
        [HttpPost]
        public IHttpActionResult SignIn(User user)
        {
            if (!UserExists(user.Username))
                return NotFound();

            user.Password = Secure.encryptPass(user.Username, user.Password);

                if (db.Users.Count(o => o.Username == user.Username && o.Password == user.Password) > 0)
                {
                    User userInfo = db.Users.Where(o => o.Username == user.Username).Single();

                    result.Add("Id", userInfo.Id);
                    result.Add("Username", userInfo.Username);
                    result.Add("Admin", userInfo.Admin);
                    result.Add("Subject", userInfo.Subject);
                    result.Add("ApiKey", userInfo.ApiKey.Key);

                    return Ok(result);
                }

            return Unauthorized();
        }

        // Registers new member, requires all details
        // POST: api/Users
        [ResponseType(typeof(void))]
        [HttpPost]
        public IHttpActionResult RegisterUser(User user, String ApiKey)
        {
            if (Authorizer.authorize("Admin", ApiKey) == 401)
                return Unauthorized();


            if (UserExists(user.Username)) //Checks if username exists
                return Conflict();

           //Check password then encrypt
           user.Password = Secure.encryptPass(user.Username, user.Password);

           if (user.Password == "Too short")
               return BadRequest();

           //Generate API Key
            db.Users.Add(user);
            db.SaveChanges();

            ApiKey newKey = new ApiKey { UserId = user.Id, Key = "TomatoJuice"};

            db.ApiKeys.Add(newKey);
            db.SaveChanges();

            return StatusCode(HttpStatusCode.Created);
        }

        /*
        //Updates user, requires all fields to be filled. All will be updated even if not changed
        // PUT: api/Users/5
        [ResponseType(typeof(void))]
        [HttpPut]
        public HttpResponseMessage PutUser(int id, User user, String ApiKey = "")
        {
            if (Authorizer.authorize("Admin", ApiKey) == 401)
                return Request.CreateErrorResponse(HttpStatusCode.Unauthorized, "Unauthorized");

            if (!ModelState.IsValid)
            {
                return Request.CreateErrorResponse(HttpStatusCode.BadRequest, ModelState);
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

        */

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