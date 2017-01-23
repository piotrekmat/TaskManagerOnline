using Newtonsoft.Json.Linq;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace CheckComputer
{
    class Model
    {
        taskmanager.ComputerService client;
        List<ComputerInformation> computers;

        public Model()
        {
            client = new taskmanager.ComputerService();
            computers = new List<ComputerInformation>();
        }
        public List<ComputerInformation> TakeDataAboutComputers()
        {
            ComputerInformation tempComputer = new ComputerInformation();
            JArray v = JArray.Parse(client.getList());
            ;
            foreach (var item in v.Children())
            {
                var itemProperties = item.Children<JProperty>();
                tempComputer.IdComputer = itemProperties.FirstOrDefault(x => x.Name == "id_computer").FirstOrDefault().ToString();
                tempComputer.ComputerName= itemProperties.FirstOrDefault(x => x.Name == "computer_name").FirstOrDefault().ToString();
                tempComputer.CpuAvg= itemProperties.FirstOrDefault(x => x.Name == "cpu_avg").FirstOrDefault().ToString();
                tempComputer.RamAvg = itemProperties.FirstOrDefault(x => x.Name == "ram_avg").FirstOrDefault().ToString();
                tempComputer.ProcessAvg = itemProperties.FirstOrDefault(x => x.Name == "process_avg").FirstOrDefault().ToString();
                tempComputer.Id = itemProperties.FirstOrDefault(x => x.Name == "id").FirstOrDefault().ToString();
                computers.Add(tempComputer);
            }
            return computers;
        }

    }
}
