using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using System.Web.Http;
using System.Net.Http;
using Newtonsoft.Json;
using Newtonsoft.Json.Linq;

namespace WebAPI.Models
{
    public class Message
    {
        public int Response { get; set; }
        public Object Msg { get; set; }

        public Message()
        {

        }

        public Message(string type, Object obj){
            switch (type)
            {
                case "Success":
                    this.Response = 200;
                    this.Msg = obj;
                    break;
                case "UserExists":
                    this.Response = 200;
                    this.Msg = "Username already exists";
                    break;
                case "UnAuthorized":
                    this.Response = 401;
                    this.Msg = "Not Authorized to pull this data";
                    break;
                case "MissingAPI":
                    this.Response = 401;
                    this.Msg = "Missing API Key";
                    break;
                case "MethodNotFound":
                    this.Response = 404;
                    this.Msg = "Method not found";
                    break;
                case "ResourceNotFound":
                    this.Response = 404;
                    this.Msg = "Resource not found";
                    break;
            }
        }

    }
}