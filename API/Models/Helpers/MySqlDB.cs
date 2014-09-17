using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using System.Data;
using System.Configuration;
using MySql.Data.MySqlClient;

namespace API.Models.Helpers
{
    public class MySqlDB
    {
        private MySqlConnection connection;
        private string server;
        private string database;
        private string uid;
        private string password;
                //Constructor

        public MySqlDB()
        {
            Initialize();
        }

        //Initialize values
        private void Initialize()
        {
            server = "localhost";
            database = "capstone";
            uid = "root";
            password = "";

            string connectionString;
            connectionString = "SERVER=" + server + ";" + "DATABASE=" + 
		    database + ";" + "UID=" + uid + ";" + "PASSWORD=" + password + ";";

            connection = new MySqlConnection(connectionString);
        }

        //open connection to database
        private int OpenConnection()
        {
            try
            {
                connection.Open();
                return 200;
            }
            catch (MySqlException ex)
            {
                //0: Cannot connect to server.
                //1045: Invalid user name and/or password.
                return ex.Number;
            }
        }

        //Close connection
        private int CloseConnection()
        {
            try
            {
                connection.Close();
                return 200;
            }
            catch (MySqlException ex)
            {
                return ex.Number;
            }
        }
    }
}