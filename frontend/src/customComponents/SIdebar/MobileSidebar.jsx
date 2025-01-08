import React, { useState } from "react";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { NavLink } from "react-router-dom";
import {
  Sheet,
  SheetClose,
  SheetContent,
  SheetDescription,
  SheetFooter,
  SheetHeader,
  SheetTitle,
  SheetTrigger,
} from "@/components/ui/sheet";
import {
  Minus,
  LayoutDashboard,
  Users,
  Network,
  SquareUserRound,
  CircleGauge,
} from "lucide-react";
import { ScrollArea } from "@/components/ui/scroll-area";
import { AlignJustify } from "lucide-react";
import { IoIosArrowDown } from "react-icons/io";
import { IoLogoSlack } from "react-icons/io";

const MobileSidebar = ({ open, setOpen }) => {
  const [activeParent, setActiveParent] = useState(null);

  const items = [
    {
      name: "Home",
      path: "/",
      logo: <LayoutDashboard size={20} />,
    },
    {
      name: "User Management",
      path: "#",
      logo: <Users size={20} />,
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
      logo: <CircleGauge size={20} />,
    },
    {
      name: "Services",
      path: "/services",
      logo: <Network size={20} />,
    },
    {
      name: "Contact",
      path: "/contact",
      logo: <SquareUserRound size={20} />,
    },
  ];

  const toggleChildren = (itemName) => {
    setActiveParent((prev) => (prev === itemName ? null : itemName)); // If same item clicked, close, else open it
  };

  return (
    <>
      <Sheet open={open} onOpenChange={setOpen}>
        <SheetTrigger asChild>
          <Button variant="outline" className=" block md:hidden">
            <AlignJustify color="red" size={48} />
          </Button>
        </SheetTrigger>

        <SheetContent side="left" className="dark:bg-gray-800">
          <SheetHeader>
            <SheetTitle className="text-left">Project</SheetTitle>
          </SheetHeader>
          <ScrollArea className="h-full pr-3">
            <ul className="pb-32 ">
              {/* mt-10 about */}
              {items.map((item, index) => {
                return (
                  <div key={index}>
                    {/* Parent item */}
                    {item.children ? (
                      <NavLink
                        className=" flex my-2 hover:text-blue-600 text-base px-1 py-2 hover:bg-gray-200 rounded items-center"
                        to={item.path || "#"}
                        onClick={() =>
                          item.children && toggleChildren(item.name)
                        } // Toggle children visibility on click
                      >
                        <p className="text-xl px-1">{item.logo}</p>
                        <div
                          className={`w-full px-2 flex justify-between items-center`}
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
                    ) : (
                      <NavLink
                        className={({ isActive }) =>
                          ` flex my-2 px-1  py-2 hover:text-blue-600  hover:bg-gray-200 text-base rounded items-center ${
                            isActive &&
                            "bg-gray-200 text-blue-600 dark:bg-gray-200"
                          }`
                        }
                        to={item.path || "#"}
                        onClick={() =>
                          item.children && toggleChildren(item.name)
                        } // Toggle children visibility on click
                      >
                        <p className="text-xl px-1">{item.logo}</p>
                        <div
                          className={`w-full px-2 flex justify-between items-center`}
                        >
                          <p>{item.name}</p>
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
                              ` ${
                                isActive &&
                                "text-blue-600 dark:text-blue-600 bg-gray-200  dark:bg-gray-200"
                              } pl-8 w-full py-1 my-2 gap-2 hover:text-blue-600 hover:bg-gray-200 rounded flex items-center text-sm text-gray-600 dark:text-gray-400 dark:hover:text-blue-600`
                            }
                            to={child.path}
                          >
                            <p className="">
                              <Minus size={16} />
                            </p>
                            <p
                              className={`
                           text-sm`}
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
        </SheetContent>
      </Sheet>
    </>
  );
};

export default MobileSidebar;

{
  /* duration-300 transition-all pt-3.5  min-h-screen dark:bg-gray-800 bg-white */
}
