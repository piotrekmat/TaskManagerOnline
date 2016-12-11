using System;
using System.Collections.Generic;
using System.Data.SqlClient;
using System.Diagnostics;
using System.IO;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Windows.Forms;


namespace WindowsApp
{
    class TaskManager
    {
        private string id;
        private PerformanceCounter cpuCounter;
        private PerformanceCounter ramCounter;
        private PerformanceCounter[] hddCounters;

        private Timer timer;


        public TaskManager()
        {
            InitializeID();
            InitializeCpuCounter();
            InitializeRamCounter();
            InitializeHddCounters();

            timer = new Timer();
            timer.Interval = 10000;
            timer.Tick += Timer_Tick;
            timer.Start();
        }


        private void InitializeID()
        {
            string[] lines = File.ReadAllLines("settings.txt");
            if (lines.Length == 1)
            {
                id = Guid.NewGuid().ToString();
                File.AppendAllText("settings.txt", Environment.NewLine + id);
            }
            else
            {
                id = lines[1];
            }
        }

        private void InitializeCpuCounter()
        {
            cpuCounter = new PerformanceCounter();
            cpuCounter.CategoryName = "Procesor";
            cpuCounter.CounterName = "Czas procesora (%)";
            cpuCounter.InstanceName = "_Total";
            cpuCounter.NextValue();
        }

        private void InitializeRamCounter()
        {
            ramCounter = new PerformanceCounter();
            ramCounter.CategoryName = "Pamięć";
            ramCounter.CounterName = "Dostępna pamięć (MB)";
            ramCounter.NextValue();
        }

        private void InitializeHddCounters()
        {
            string[] drives = Environment.GetLogicalDrives();
            hddCounters = new PerformanceCounter[drives.Length];
            for (int i = 0; i < drives.Length; i++)
            {
                hddCounters[i] = new PerformanceCounter();
                hddCounters[i].CategoryName = "Dysk logiczny";
                hddCounters[i].CounterName = "Wolne megabajty";
                hddCounters[i].InstanceName = drives[i].Substring(0, 2);
                hddCounters[i].NextValue();
            }
        }


        public string GetComputerID()
        {
            return id;
        }

        public string GetComputerName()
        {
            return Environment.MachineName;
        }

        public string GetUserName()
        {
            return Environment.UserName;
        }

        public int GetCpuPercent()
        {
            return (int)Math.Round(cpuCounter.NextValue(), 0);
        }

        public int GetRamMB()
        {
            return (int)Math.Round(ramCounter.NextValue(), 0);
        }

        public int[] GetHddsMB()
        {
            int[] tab = new int[hddCounters.Length];
            for(int i = 0; i < hddCounters.Length; i++)
            {
                tab[i] = (int)Math.Round(hddCounters[i].NextValue(), 0);
            }
            return tab;
        }

        public string[] GetProcesses()
        {
            Process[] processes = Process.GetProcesses();
            string[] tab = new string[processes.Length];
            for (int i = 0; i < processes.Length; i++)
            {
                tab[i] = processes[i].ProcessName;
            }
            return tab;
        }


        private void Timer_Tick(object sender, EventArgs e)
        {
            try
            {
                SqlConnection connection = new SqlConnection("Data Source = SQL5032.SmarterASP.NET; Initial Catalog = DB_A14E76_baza; User Id = DB_A14E76_baza_admin; Password = Injakopr1;");

                connection.Open();

                string Komputer_Id = "'" + GetComputerID() + "'";
                string Komputer_Nazwa = "'" + GetComputerName() + "'";
                string Uzytkownik_Nazwa = "'" + GetUserName() + "'";
                int Procesor_Procent = GetCpuPercent();
                int RAM_MB = GetRamMB();
                string HDD_MB = "'";
                foreach (int hdd in GetHddsMB())
                {
                    HDD_MB += hdd + ";";
                }
                HDD_MB += "'";
                string Procesy = "'";
                foreach (string proces in GetProcesses())
                {
                    Procesy += proces + ";";
                }
                Procesy += "'";
                //string commandString = string.Format("INSERT INTO [Dane] ([Komputer_Id],[Komputer_Nazwa],[Uzytkownik_Nazwa],[Procesor_Procent],[RAM_MB],[HDD_MB],[Procesy]) "
                //                                    + "VALUES ({0},{1},{2},{3},{4},{5},{6})", Komputer_Id, Komputer_Nazwa, Uzytkownik_Nazwa, Procesor_Procent, RAM_MB, HDD_MB, Procesy);


                string commandString = string.Format("INSERT INTO [Dane] ([Komputer_Id],[Komputer_Nazwa],[Uzytkownik_Nazwa],[Procesor_Procent],[RAM_MB],[HDD_MB],[Procesy]) "
                                                    + "VALUES ({0},{1},{2},{3},{4},{5},{6})", Komputer_Id, Komputer_Nazwa, Uzytkownik_Nazwa, Procesor_Procent, RAM_MB, HDD_MB, Procesy);

                SqlCommand command = new SqlCommand(commandString, connection);
                command.ExecuteNonQuery();

                connection.Close();
            }
            catch
            {

            }
        }

    }
}
