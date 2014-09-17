using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace API.Models.Helpers
{
    public interface IRepository<T>
    {
        T Add(T item);
        T Update(T item);
        bool Remove(Guid id);
    }
}