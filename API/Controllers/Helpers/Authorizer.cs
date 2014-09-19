using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using API.Models;
using Newtonsoft.Json.Linq;

namespace API.Controllers.Helpers
{
    public class Authorizer
    {
        public static int authorize(String level, String apiKey)
        {
            int status;

            switch (level)
            {
                case "Admin": status = verifyAdmin(apiKey); break;
                case "User": status = verifyApiKey(apiKey); break;
                default: status = verifyAdmin(apiKey); break;
            }

            return status;
        }

        //Verifys if ApiKey is in database
        private static int verifyApiKey(String apiKey)
        {
            try
            {
                ApiKey info = getApiKeyInfo(apiKey);
                if (info.Key == "null")
                    return 401;

                return 200;
            }
            catch
            {
                return 401;
            }

        }

        private static int verifyAdmin(String apiKey)
        {
            ApiKey info = getApiKeyInfo(apiKey);

            if (info.Key == "null")
                return 401;

            try
            {
                using (var db = new DB())
                {

                    if (db.Users.Find(info.UserId).Admin > 0)
                        return 200;
                    else
                        return 401;
                }
            }
            catch
            {
                return 401;
            }
        }

        private static ApiKey getApiKeyInfo(String apiKey)
        {
            ApiKey keyInfo = new ApiKey();

            try
            {
                using (var db = new DB())
                {
                    keyInfo = db.ApiKeys.Where(o => o.Key == apiKey).Single();
                }
                return keyInfo;
            }
            catch
            {
                keyInfo.UserId = 0;
                keyInfo.Key = "null";
                return keyInfo;
            }
        }
    }
}