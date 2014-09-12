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
        public string Msg { get; set; }

        public Message(string type){
            switch (type)
            {
                case "UserExists":
                    this.Response = 200;
                    this.Msg = "Username already exists";
                    break;
                case "Unauthorized":
                    this.Response = 401;
                    this.Msg = "Unauthorized";
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