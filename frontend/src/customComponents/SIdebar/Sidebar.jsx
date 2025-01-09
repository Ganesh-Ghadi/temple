import { useState } from "react";
import { IoIosArrowDropleft } from "react-icons/io";
import { IoLogoSlack } from "react-icons/io";
import { NavLink } from "react-router-dom";
import { IoIosArrowDown } from "react-icons/io";
import {
  Minus,
  Settings,
  LayoutDashboard,
  Users,
  Sun,
  Network,
  SquareUserRound,
  CircleGauge,
  CircleChevronLeft,
} from "lucide-react";
import { ScrollArea } from "@/components/ui/scroll-area";
import { useSelector } from "react-redux";

const Sidebar = ({ isSidebarOpen, setIsSidebarOpen }) => {
  // State to track which parent item has its children visible
  const [activeParent, setActiveParent] = useState(null);

  const items = [
    {
      name: "Home",
      path: "/",
      logo: <LayoutDashboard size={16} />,
    },
    {
      name: "Masters",
      path: "#",
      logo: <Settings size={16} />,
      children: [
        {
          name: "Devtas",
          path: "/devtas",
          logo: <Sun />,
        },
        {
          name: "Pooja Types",
          path: "/pooja_types",
          logo: <Sun />,
        },
      ],
    },
    {
      name: "User Management",
      path: "#",
      logo: <Users size={16} />,
      children: [
        {
          name: "Roles",
          path: "/roles",
          logo: <IoLogoSlack />,
        },
        {
          name: "Users",
          path: "/users",
          logo: <IoLogoSlack />,
        },
      ],
    },
    {
      name: "Dashboard",
      path: "/gr",
      logo: <CircleGauge size={16} />,
    },
    {
      name: "Services",
      path: "/services",
      logo: <Network size={16} />,
    },
    {
      name: "Contact",
      path: "/contact",
      logo: <SquareUserRound size={16} />,
    },
  ];

  // Function to toggle children visibility (close previous and open current)
  const toggleChildren = (itemName) => {
    setActiveParent((prev) => (prev === itemName ? null : itemName)); // If same item clicked, close, else open it
  };

  return (
    <>
      <ScrollArea
        className={`${
          isSidebarOpen ? "w-80 px-4" : " w-16 p-3 "
        } hidden md:block duration-300 text-white transition-all pt-3.5 border border-dark-purple  min-h-screen dark:bg-gray-800 bg-dark-purple`}
        // className={`${
        //   isSidebarOpen ? "w-80 opacity-100" : "w-0 opacity-0"
        // } duration-300 transition-all px-4 pt-3.5 shadow-xl min-h-screen dark:bg-gray-800 bg-slate-50`}
      >
        <div className="flex gap-x-4 items-center">
          <p className="text-4xl p-1">
            <LayoutDashboard />
          </p>
          <p className={`text-3xl duration-300 ${!isSidebarOpen && "scale-0"}`}>
            Designer
          </p>
        </div>
        <ul className="pb-32 pt-8">
          {/* mt-10 about */}
          {items.map((item, index) => {
            return (
              <div key={index}>
                {/* Parent item */}
                {item.children ? (
                  <NavLink
                    className=" flex my-2 text-sm px-1 py-2 hover:bg-dark-purple-light rounded items-center"
                    to={item.path || "#"}
                    onClick={() => item.children && toggleChildren(item.name)} // Toggle children visibility on click
                  >
                    <p className="text-xl px-1">{item.logo}</p>
                    <div
                      className={`w-full px-2 flex justify-between items-center ${
                        !isSidebarOpen && "opacity-0 invisible"
                      }`}
                    >
                      <p
                        className={`font-medium ${
                          !isSidebarOpen &&
                          "text-ellipsis whitespace-nowrap overflow-hidden"
                        }`}
                      >
                        {item.name}
                      </p>
                      {item.children && (
                        <p>
                          <IoIosArrowDown
                            className={`${
                              item.children &&
                              activeParent === item.name &&
                              "rotate-180"
                            }`}
                          />
                        </p>
                      )}
                    </div>
                  </NavLink>
                ) : (
                  <NavLink
                    className={({ isActive }) =>
                      ` flex my-2 px-1 py-2 hover:text-white  hover:bg-dark-purple-light text-sm rounded items-center ${
                        isActive && "bg-gray-200 text-black  dark:bg-gray-200"
                      }`
                    }
                    to={item.path || "#"}
                    onClick={() => item.children && toggleChildren(item.name)} // Toggle children visibility on click
                  >
                    <p className="text-xl px-1">{item.logo}</p>
                    <div
                      className={`w-full px-2 flex justify-between items-center ${
                        !isSidebarOpen && "opacity-0 invisible"
                      }`}
                    >
                      <p className="">{item.name}</p>
                      {item.children && (
                        <p>
                          <IoIosArrowDown
                            className={`${
                              item.children &&
                              activeParent === item.name &&
                              "rotate-180"
                            }`}
                          />
                        </p>
                      )}
                    </div>
                  </NavLink>
                )}
                {/* Render children if the parent item has children and it's the active one */}
                {item.children && activeParent === item.name && (
                  <div>
                    {item.children.map((child, idx) => (
                      <NavLink
                        key={idx}
                        className={({ isActive }) =>
                          `  ${
                            isActive &&
                            " bg-gray-200 text-black dark:bg-gray-200"
                          } pl-1 w-full py-1 my-2 gap-2 hover:text-blue-600  hover:bg-gray-200 rounded flex items-center text-sm text-gray-600 dark:text-gray-400 dark:hover:text-blue-600   ${
                            isSidebarOpen && "pl-8"
                          } `
                        }
                        to={child.path}
                      >
                        <p className="">
                          <Minus size={16} />
                        </p>
                        <p
                          className={`${!isSidebarOpen && "hidden"}
                          origin-left duration-300 text-sm`}
                        >
                          {child.name}
                        </p>
                      </NavLink>
                    ))}
                  </div>
                )}
              </div>
            );
          })}
        </ul>
      </ScrollArea>
    </>
  );
};

export default Sidebar;
